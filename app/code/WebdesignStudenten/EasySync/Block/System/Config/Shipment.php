<?php
namespace WebdesignStudenten\EasySync\Block\System\Config;
class Shipment extends \WebdesignStudenten\EasySync\Block\System\Config\Button
{
    protected $_template = 'WebdesignStudenten_EasySync::system/config/shipment.phtml';
    public function getAjaxUrl()
    {
        return $this->getUrl('datasync/sync/shipmentDataSync');
    }
    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            [
                'id' => 'shipmentSync',
                'label' => __('Sync Now'),
            ]
        );

        return $button->toHtml();
    }
}