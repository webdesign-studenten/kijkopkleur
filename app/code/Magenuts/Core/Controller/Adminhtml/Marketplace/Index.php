<?php
namespace Magenuts\Core\Controller\Adminhtml\Marketplace;

use Magento\Framework\App\Response\RedirectInterface;

class Index extends \Magento\Backend\App\Action
{

    protected $resultPageFactory;
    protected $_redirect;
    
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        RedirectInterface $redirect)
            
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->_redirect = $redirect;
        parent::__construct($context);
    }

    public function execute()
    {
        return $this->_redirect('http://magenuts.com/');
    }
}
