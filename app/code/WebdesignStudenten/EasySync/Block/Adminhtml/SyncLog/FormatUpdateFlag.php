<?php

namespace WebdesignStudenten\EasySync\Block\Adminhtml\SyncLog;

class FormatUpdateFlag extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
    * Renders grid column
    * @param \Magento\Framework\DataObject $row
    * @return string
    */
    public function render(\Magento\Framework\DataObject $row)
    {
        return ($row['UpdateFlag'] == '1') ? __('Pending') : __('Completed');
    }

}