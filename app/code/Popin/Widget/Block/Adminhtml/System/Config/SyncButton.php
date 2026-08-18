<?php

namespace Popin\Widget\Block\Adminhtml\System\Config;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\FormKey;

class SyncButton extends Field
{
    protected $_template = 'Popin_Widget::system/config/sync_button.phtml';

    public function __construct(
        Context $context,
        private readonly FormKey $formKey,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Render the field without the scope/inheritance columns (it is an action, not a value).
     */
    public function render(AbstractElement $element): string
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();

        return parent::render($element);
    }

    protected function _getElementHtml(AbstractElement $element): string
    {
        return $this->_toHtml();
    }

    /**
     * Controller URL, carrying the config page's current scope so the right
     * store's token and products are used.
     */
    public function getAjaxUrl(): string
    {
        $params = [];
        if ($store = $this->getRequest()->getParam('store')) {
            $params['store'] = $store;
        } elseif ($website = $this->getRequest()->getParam('website')) {
            $params['website'] = $website;
        }

        return $this->getUrl('popin/sync/products', $params);
    }

    public function getFormKeyValue(): string
    {
        return $this->formKey->getFormKey();
    }

    public function getButtonId(): string
    {
        return 'popin_sync_products_button';
    }
}
