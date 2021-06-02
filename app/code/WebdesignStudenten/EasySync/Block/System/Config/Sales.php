<?php
namespace WebdesignStudenten\EasySync\Block\System\Config;
class Sales extends \WebdesignStudenten\EasySync\Block\System\Config\Button
{
    protected $_template = 'WebdesignStudenten_EasySync::system/config/sales.phtml';
    public function getAjaxUrl()
    {
        return $this->getUrl('datasync/sync/salesDataSync');
    }
    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            [
                'id' => 'salesSync',
                'label' => __('Sync Now'),
            ]
        );

        return $button->toHtml();
    }
}