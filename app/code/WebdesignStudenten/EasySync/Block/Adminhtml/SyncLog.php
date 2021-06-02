<?php
namespace WebdesignStudenten\EasySync\Block\Adminhtml;
class SyncLog extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
		
        $this->_controller = 'adminhtml_syncLog';/*block grid.php directory*/
        $this->_blockGroup = 'WebdesignStudenten_EasySync';
        $this->_headerText = __('Sync Log');
        // $this->_addButtonLabel = __('Download Attibute Sets'); 
        $this->removeButton(\Magento\Backend\Block\Widget\Grid\Container::PARAM_BUTTON_NEW);
        parent::_construct();
		
    }
}
