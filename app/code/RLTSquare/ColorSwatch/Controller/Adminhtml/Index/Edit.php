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

/**
 * Class Edit
 * @package RLTSquare\ColorSwatch\Controller\Adminhtml\Index
 */
class Edit extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var \RLTSquare\ColorSwatch\Model\ColorSwatch
     */
    protected $modelColorSwatch;
    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $session;
    /**
     * @param Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        Action\Context $context,
        \RLTSquare\ColorSwatch\Model\ColorSwatch $modelColorSwatch,
        \Magento\Backend\Model\Session $session,
        \RLTSquare\ColorSwatch\Model\ResourceModel\ColorSwatch $resourceModel,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->session = $session;
        $this->resourceModel = $resourceModel;
        $this->modelColorSwatch = $modelColorSwatch;
        parent::__construct($context);
    }
    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('RLTSquare_ColorSwatch::colorswatch');
    }
    /**
     * Init actions
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('RLTSquare_ColorSwatch::colorswatch')
            ->addBreadcrumb(__('Color Group'), __('Color Group'))
            ->addBreadcrumb(__('Manage Color Group'), __('Manage Color Group'));
        return $resultPage;
    }
    /**
     * Edit CMS page
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('colorswatch_id');
        $model = $this->modelColorSwatch;

        // 2. Initial checking
        if ($id) {

            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This record no longer exists.'));
                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }
        // 3. Set entered data if was error when we do save
        $data = $this->session->getFormData(true);
        if (!empty($data)) {

            $model->setData($data);

        }
        // 4. Register model to use later in blocks
        $this->_coreRegistry->register('rltsquare_colorswatch', $model);
        // 5. Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Color Group') : __('New Color Group'),
            $id ? __('Edit Color Group') : __('New Color Group')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Color Group'));
        $optionvalue = $this->resourceModel->getOptionValues($model->getBrandId());
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getId() ? __('Edit "%1"', $optionvalue['0']['value']) : __('New Color Group'));
        return $resultPage;
    }
}
