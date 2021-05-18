<?php

namespace Packs\Magento2\Controller\Adminhtml\Shipment;


use Magento\Backend\App\Action;
use Packs\Magento2\Model\Export\Shipments as ExportShipmentsModel;
use Packs\Magento2\Model\Save\Labels as PacksImportLabelsModel;

class Save extends Action
{
    protected $_exportShipmentsModel;
    protected $_packsImportLabelModel;


    public function __construct(
        Action\Context $context,
        ExportShipmentsModel $exportShipmentsModel,
        PacksImportLabelsModel $packsImportLabelModel
    ) {
        $this->_exportShipmentsModel = $exportShipmentsModel;
        $this->_packsImportLabelModel = $packsImportLabelModel;
        parent::__construct($context);
    }

    public function execute()
    {
        $postData = $this->getRequest()->getPostValue();
        $this->_exportShipmentsModel->startExport($postData);

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('sales/order/index');
        return $resultRedirect;
    }
}
