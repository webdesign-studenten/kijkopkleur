<?php
/**
 *
 * @category : RLTSquare
 * @Package  : RLTSquare_ColorSwatch
 * @Author   : RLTSquare <support@rltsquare.com>
 * @copyright Copyright 2021 © rltsquare.com All right reserved
 * @license https://rltsquare.com/
 */
namespace RLTSquare\ColorSwatch\Controller\Adminhtml\Index;
use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;

/**
 * Class ImportIndex
 * @package RLTSquare\ColorSwatch\Controller\Adminhtml\Index
 */
class ImportIndex extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * ImportIndex constructor.
     * @param Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * @return mixed
     */
    public function execute()
    {
        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()->prepend(__('ColorSwatch Imports'));
        return $resultPage;
    }

    /**
     * @return mixed
     */
    protected function _initAction()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('RLTSquare_ColorSwatch::colorswatch');
        $resultPage->addBreadcrumb(__('ColorSwatch Imports'), __('ColorSwatch Imports'));
        return $resultPage;
    }
}
