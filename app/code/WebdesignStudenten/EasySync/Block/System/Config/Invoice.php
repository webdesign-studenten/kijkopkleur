<?php
namespace WebdesignStudenten\EasySync\Block\System\Config;
class Invoice extends \WebdesignStudenten\EasySync\Block\System\Config\Button
{
    protected $_template = 'WebdesignStudenten_EasySync::system/config/invoice.phtml';
    public function getAjaxUrl()
    {
        return $this->getUrl('datasync/sync/invoiceDataSync');
    }
    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            [
                'id' => 'invoiceSync',
                'label' => __('Sync Now'),
            ]
        );

        return $button->toHtml();
    }
}