<?php
namespace WebdesignStudenten\EasySync\Controller\Adminhtml;

class Customer extends \Magento\Backend\App\Action
{
    public function __construct(
        \Magento\Backend\App\Action\Context $context
    ) {
        parent::__construct($context);
    }
    public function execute()
    {
        echo 'hello';
        die;
        // Code to perform specific action    
    }
}