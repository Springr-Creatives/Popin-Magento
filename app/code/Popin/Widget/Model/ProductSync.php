<?php

namespace Popin\Widget\Model;

use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Pushes a store's catalog products to the Popin backend.
 *
 * Mirrors the Popin Shopify app import: each product becomes one record keyed by
 * (seller_id, external_id). Configurable/grouped children ("variants") are skipped
 * by filtering out products that are not individually visible, so each parent
 * product is sent once with its own price and image.
 */
class ProductSync
{
    private const ENDPOINT_PATH = '/api/v1/products/update';
    private const DEFAULT_API_URL = 'https://widget01.popin.to';

    /** Products per Popin API request. */
    private const BATCH_SIZE = 250;

    /** Products loaded per catalog collection page (memory bound). */
    private const PAGE_SIZE = 500;

    /** Popin stores price as an integer in minor units (e.g. paise/cents). */
    private const PRICE_MULTIPLIER = 100;

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly StoreManagerInterface $storeManager,
        private readonly ProductCollectionFactory $productCollectionFactory,
        private readonly Emulation $emulation,
        private readonly Curl $curl,
        private readonly Json $json,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * Sync all eligible products of the given store to Popin.
     *
     * @return int Number of products sent.
     * @throws LocalizedException
     */
    public function execute(int $storeId): int
    {
        $token = $this->getToken($storeId);
        if ($token === '') {
            throw new LocalizedException(
                __('Please configure your Popin Token before syncing products.')
            );
        }

        $endpoint = $this->getEndpoint($storeId);

        // Emulate the storefront so getProductUrl()/media URLs resolve against the
        // store's real base URL and URL rewrites instead of the admin context.
        $this->emulation->startEnvironmentEmulation($storeId, Area::AREA_FRONTEND, true);
        try {
            $mediaBaseUrl = $this->storeManager->getStore($storeId)
                ->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);

            $sent = 0;
            $batch = [];

            foreach ($this->getProducts($storeId) as $product) {
                $batch[] = [
                    'seller_id'   => $token,
                    'external_id' => (string) $product->getId(),
                    'name'        => (string) $product->getName(),
                    'url'         => (string) $product->getProductUrl(),
                    'price'       => (int) round((float) $product->getFinalPrice() * self::PRICE_MULTIPLIER),
                    'image'       => $this->getImageUrl($product, $mediaBaseUrl),
                ];

                if (count($batch) >= self::BATCH_SIZE) {
                    $this->push($endpoint, $batch);
                    $sent += count($batch);
                    $batch = [];
                }
            }

            if ($batch) {
                $this->push($endpoint, $batch);
                $sent += count($batch);
            }
        } finally {
            $this->emulation->stopEnvironmentEmulation();
        }

        return $sent;
    }

    /**
     * Yield eligible products for the store, one page at a time.
     *
     * @return \Generator|\Magento\Catalog\Model\Product[]
     */
    private function getProducts(int $storeId): \Generator
    {
        $collection = $this->productCollectionFactory->create();
        $collection->setStoreId($storeId);
        $collection->addStoreFilter($storeId);
        $collection->addAttributeToSelect(['name', 'image', 'price', 'url_key']);
        $collection->addAttributeToFilter('status', Status::STATUS_ENABLED);
        // Skip variants: children of configurable/grouped products are "not visible
        // individually"; only individually-visible products are sent.
        $collection->addAttributeToFilter('visibility', [
            'in' => [
                Visibility::VISIBILITY_IN_CATALOG,
                Visibility::VISIBILITY_IN_SEARCH,
                Visibility::VISIBILITY_BOTH,
            ],
        ]);
        $collection->addFinalPrice();
        $collection->setPageSize(self::PAGE_SIZE);

        $lastPage = $collection->getLastPageNumber();
        for ($page = 1; $page <= $lastPage; $page++) {
            $collection->setCurPage($page);
            $collection->clear();
            foreach ($collection as $product) {
                yield $product;
            }
        }
    }

    private function getImageUrl($product, string $mediaBaseUrl): string
    {
        $image = $product->getImage();
        if (!$image || $image === 'no_selection') {
            return '';
        }

        return rtrim($mediaBaseUrl, '/') . '/catalog/product' . $image;
    }

    private function push(string $endpoint, array $products): void
    {
        $this->curl->addHeader('Content-Type', 'application/json');
        $this->curl->addHeader('Accept', 'application/json');
        $this->curl->setOption(CURLOPT_TIMEOUT, 60);
        $this->curl->post($endpoint, $this->json->serialize(['products' => $products]));

        $status = $this->curl->getStatus();
        if ($status < 200 || $status >= 300) {
            $this->logger->error('Popin product sync failed', [
                'endpoint' => $endpoint,
                'http_status' => $status,
                'response' => $this->curl->getBody(),
                'count' => count($products),
            ]);
            throw new LocalizedException(
                __('Popin rejected the sync (HTTP %1). Please try again or contact Popin support.', $status)
            );
        }
    }

    private function getToken(int $storeId): string
    {
        return trim((string) $this->scopeConfig->getValue(
            'popin_widget/general/token',
            ScopeInterface::SCOPE_STORE,
            $storeId
        ));
    }

    private function getEndpoint(int $storeId): string
    {
        $base = trim((string) $this->scopeConfig->getValue(
            'popin_widget/general/api_url',
            ScopeInterface::SCOPE_STORE,
            $storeId
        ));
        if ($base === '') {
            $base = self::DEFAULT_API_URL;
        }

        return rtrim($base, '/') . self::ENDPOINT_PATH;
    }
}
