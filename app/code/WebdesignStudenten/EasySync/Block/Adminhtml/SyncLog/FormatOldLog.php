<?php

namespace WebdesignStudenten\EasySync\Block\Adminhtml\SyncLog;

class FormatOldLog extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
    * Renders grid column
    * @param \Magento\Framework\DataObject $row
    * @return string
    */
    public function render(\Magento\Framework\DataObject $row)
    {
        return html_entity_decode($row['OldValue']);
    }

}