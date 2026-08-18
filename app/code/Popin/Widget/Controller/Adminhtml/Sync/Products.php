<?php

namespace Popin\Widget\Controller\Adminhtml\Sync;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;
use Popin\Widget\Model\ProductSync;
use Psr\Log\LoggerInterface;

class Products extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Popin_Widget::config';

    public function __construct(
        Context $context,
        private readonly JsonFactory $resultJsonFactory,
        private readonly ProductSync $productSync,
        private readonly StoreManagerInterface $storeManager,
        private readonly LoggerInterface $logger
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();

        try {
            $storeId = $this->resolveStoreId();
            $count = $this->productSync->execute($storeId);

            return $result->setData([
                'success' => true,
                'message' => (string) __('%1 product(s) synced to Popin.', $count),
            ]);
        } catch (LocalizedException $e) {
            return $result->setData([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        } catch (\Throwable $e) {
            $this->logger->error(
                'Popin product sync error: ' . $e->getMessage(),
                ['exception' => $e]
            );

            return $result->setData([
                'success' => false,
                'message' => (string) __('An unexpected error occurred while syncing products.'),
            ]);
        }
    }

    /**
     * Resolve the concrete store view whose products (and Popin token) should be synced,
     * from the config page's scope switcher.
     */
    private function resolveStoreId(): int
    {
        $store = $this->getRequest()->getParam('store');
        if ($store !== null && $store !== '') {
            return (int) $this->storeManager->getStore($store)->getId();
        }

        $website = $this->getRequest()->getParam('website');
        if ($website !== null && $website !== '') {
            return (int) $this->storeManager->getWebsite($website)->getDefaultStore()->getId();
        }

        return (int) $this->storeManager->getDefaultStoreView()->getId();
    }
}
