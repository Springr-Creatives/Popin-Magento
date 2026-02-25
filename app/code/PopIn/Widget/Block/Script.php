<?php

namespace PopIn\Widget\Block;

use Magento\Framework\View\Element\Template;
use Magento\Store\Model\ScopeInterface;

class Script extends Template
{
    public function isEnabled(): bool
    {
        return (bool) $this->_scopeConfig->getValue(
            'popin_widget/general/enabled',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getToken(): string
    {
        return (string) $this->_scopeConfig->getValue(
            'popin_widget/general/token',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getMode(): string
    {
        return (string) $this->_scopeConfig->getValue(
            'popin_widget/general/mode',
            ScopeInterface::SCOPE_STORE
        );
    }

    protected function _toHtml(): string
    {
        if (!$this->isEnabled() || !$this->getToken()) {
            return '';
        }

        return parent::_toHtml();
    }
}
