<?php
namespace Packs\Magento2\Model\Config\Backend;
use \Magento\Framework\Data\OptionSourceInterface;
class ModeType implements OptionSourceInterface
{
    public function toOptionArray()
    {
        $modeType = array();
        $modeType[] = [
            'value' => 'live',
            'label' => __('Live')
        ];
        $modeType[] = [
            'value' => 'test',
            'label' => __('Test')
        ];
        return $modeType;
    }
}