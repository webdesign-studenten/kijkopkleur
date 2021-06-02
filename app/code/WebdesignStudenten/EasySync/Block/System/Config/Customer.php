<?php
namespace WebdesignStudenten\EasySync\Block\System\Config;
class Customer extends \WebdesignStudenten\EasySync\Block\System\Config\Button
{
    protected $_template = 'WebdesignStudenten_EasySync::system/config/customer.phtml';
    public function getAjaxUrl()
    {
        return $this->getUrl('datasync/sync/customerDataSync');
    }
    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            [
                'id' => 'custSync',
                'label' => __('Sync Now'),
            ]
        );

        return $button->toHtml();
    }
}