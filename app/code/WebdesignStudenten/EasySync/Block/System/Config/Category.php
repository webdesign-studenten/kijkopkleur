<?php
namespace WebdesignStudenten\EasySync\Block\System\Config;
class Category extends \WebdesignStudenten\EasySync\Block\System\Config\Button
{
    protected $_template = 'WebdesignStudenten_EasySync::system/config/category.phtml';
    public function getAjaxUrl()
    {
        return $this->getUrl('datasync/sync/categoryDataSync');
    }
    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            [
                'id' => 'categorySync',
                'label' => __('Sync Now'),
            ]
        );

        return $button->toHtml();
    }
}