<?php
/**
 *
 * @category : RLTSquare
 * @Package  : RLTSquare_ColorSwatch
 * @Author   : RLTSquare <support@rltsquare.com>
 * @copyright Copyright 2021 © rltsquare.com All right reserved
 * @license https://rltsquare.com/
 */
namespace RLTSquare\ColorSwatch\Controller\Adminhtml\Import;

use Magento\Framework\Controller\ResultFactory;

/**
 * Class Imports
 * @package RLTSquare\ColorSwatch\Controller\Adminhtml\Import
 */
class Imports extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;
    /**
     * @var \RLTSquare\ColorSwatch\Model\Import\CsvImportHandler
     */
    protected $csvImportHandler;

    /**
     * Imports constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \RLTSquare\ColorSwatch\Model\Import\CsvImportHandler $csvImportHandler
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \RLTSquare\ColorSwatch\Model\Import\CsvImportHandler $csvImportHandler,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory
    ) {
        $this->fileFactory = $fileFactory;
        $this->csvImportHandler = $csvImportHandler;
        parent::__construct($context);
    }
    /**
     * import action from import/export tax
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        if ($this->getRequest()->isPost() && !empty($this->getRequest()->isPost())) {
            try {
                /** @var $importHandler \Magento\TaxImportExport\Model\Rate\CsvImportHandler */
                if($this->getRequest()->getPostValue("delete")) {
                    $isdelete=1;
                } else {
                    $isdelete=0;
                }
                $this->csvImportHandler->importFromCsvFile($this->getRequest()->getFiles('import_promotedproduct_file'));
                $this->messageManager->addSuccess(__('The Promoted Products have been imported.'));
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addError(__('Invalid file upload attempt'));
            }
        } else {
            $this->messageManager->addError(__('Invalid file upload attempt.'));
        }
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRedirectUrl());
        return $resultRedirect;
    }
    /**
     * @return bool
     */
}
