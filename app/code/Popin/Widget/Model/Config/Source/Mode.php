<?php

namespace Popin\Widget\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Mode implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        return [
            ['value' => 'hidden', 'label' => __('Hidden (Button triggered)')],
            ['value' => 'visible', 'label' => __('Visible (Auto-show launcher)')],
        ];
    }
}
