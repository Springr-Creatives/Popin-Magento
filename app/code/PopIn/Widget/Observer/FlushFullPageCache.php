<?php

namespace Popin\Widget\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\PageCache\Model\Cache\Type as FullPageCache;
use Magento\Framework\App\Cache\TypeListInterface;

class FlushFullPageCache implements ObserverInterface
{
    public function __construct(
        private readonly TypeListInterface $cacheTypeList
    ) {
    }

    public function execute(Observer $observer): void
    {
        $this->cacheTypeList->cleanType(FullPageCache::TYPE_IDENTIFIER);
    }
}
