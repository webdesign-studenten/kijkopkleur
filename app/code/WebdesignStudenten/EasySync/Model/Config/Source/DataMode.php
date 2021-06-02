<?php
namespace WebdesignStudenten\EasySync\Model\Config\Source;

class DataMode implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'sender', 'label' => __('Sender')],
            ['value' => 'receiver', 'label' => __('Receiver')]
        ];
    }
}