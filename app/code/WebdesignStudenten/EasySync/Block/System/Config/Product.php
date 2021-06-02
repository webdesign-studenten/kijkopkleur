<?php
namespace WebdesignStudenten\EasySync\Block\System\Config;
class Product extends \WebdesignStudenten\EasySync\Block\System\Config\Button
{
    protected $_template = 'WebdesignStudenten_EasySync::system/config/product.phtml';
    public function getAjaxUrl()
    {
        return $this->getUrl('datasync/sync/productDataSync');
    }
    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            [
                'id' => 'productSync',
                'label' => __('Sync Now'),
            ]
        );

        return $button->toHtml();
    }
}