<?php
namespace WebdesignStudenten\EasySync\Block\System\Config;
class Newsletter extends \WebdesignStudenten\EasySync\Block\System\Config\Button
{
    protected $_template = 'WebdesignStudenten_EasySync::system/config/newsletter.phtml';
    public function getAjaxUrl()
    {
        return $this->getUrl('datasync/sync/newsletterDataSync');
    }
    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            [
                'id' => 'newsletterSync',
                'label' => __('Sync Now'),
            ]
        );

        return $button->toHtml();
    }
}