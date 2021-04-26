<?php

namespace Packs\Magento2\Model\Download;

use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Packs\Magento2\Model\LabelsFactory;
use Packs\Magento2\Model\ShipmentFactory as PacksShipmentsFactory;
use Packs\Magento2\Helper\Adminhtml\Data as CustomHelper;
use Packs\Magento2\Model\Save\Labels as LabelsModel;

class Labels extends AbstractModel
{
    protected $_apiUser;
    protected $_apiUserPassword;
    protected $_apiUrl;
    protected $_apiAuthUrl;
    protected $_continue = true;
    protected $_response;
    protected $_pdf;

    protected $_orderIds;
    protected $_labelsFactory;
    protected $_packsShipmentsFactory;
    protected $_labelsData;
    protected $_allItemIds = array();
    protected $_customHelper;
    protected $_labelsModel;
    protected $_messageManager;
    protected $_directory;
    protected $_scopeConfig;

    public function __construct(
        Context $context,
        Registry $registry,
        ManagerInterface $messageManager,
        LabelsFactory $labelsFactory,
        PacksShipmentsFactory $packsShipmentsFactory,
        DirectoryList $directory,
        ScopeConfigInterface $scopeConfig,
        CustomHelper $customHelper,
        LabelsModel $labelsModel
    ) {
        $this->_messageManager = $messageManager;
        $this->_labelsFactory = $labelsFactory;
        $this->_packsShipmentsFactory = $packsShipmentsFactory;
        $this->_directory = $directory;
        $this->_customHelper = $customHelper;
        $this->_labelsModel = $labelsModel;
        $this->_scopeConfig = $scopeConfig;
        parent::__construct($context,$registry);
    }


    public function downloadLabels($orderIds)
    {
        return $this->preProcessingPdfCreation($orderIds);
    }

    public function preProcessingPdfCreation($orderIds){

        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $this->_apiUser = $this->_scopeConfig->getValue("packs/general/api_name", $storeScope);
        $this->_apiUserPassword = $this->_scopeConfig->getValue("packs/general/api_password", $storeScope);
        list($this->_apiUrl, $this->_apiAuthUrl) = $this->_customHelper->getApiUrls('getlabels');

        set_time_limit(720000);                 // script timeout: 15 mins.
        ini_set('display_errors', 1);
        error_reporting(E_ALL);
        ini_set('max_execution_time', 720000);
        ini_set("default_charset", "utf-8");
        ini_set("memory_limit", "-1");
        ini_set("display_errors", "on");
        ob_start();

        $this->_orderIds = $orderIds;

        return $this->processingPdfCreation();
    }

    public function processingPdfCreation(){

        $tokenData = $this->_customHelper->Authorize();
        $this->tToken = $tokenData['type'];
        $this->aToken = $tokenData['token'];

        $this->getPacksShipmentItems();

        $this->getLabelsFromApi();

        $this->createLabelsPdf();

        return $this->_pdf;
    }

    private function getPacksShipmentItems(){
        $resultFactory = $this->_packsShipmentsFactory->create();
        $labelsCollection = $resultFactory->getCollection();
        $labelsCollection->addFieldToFilter('magento_order_id', ['in' => $this->_orderIds]);
        $this->_labelsData =  $labelsCollection->getData();
        return;
    }

    public function getLabelsFromApi(){
        $missingLabels = array();
        foreach($this->_labelsData as $packsShipment){
            $packsshipmentItemsIds = explode(',',$packsShipment['packs_shipment_item_ids']);
            foreach($packsshipmentItemsIds as $packsshipmentItemId) {
                array_push($missingLabels, array('shipmentId' => $packsShipment['packs_shipment_id'], 'shipmentItemId' => $packsshipmentItemId));
                array_push($this->_allItemIds,$packsshipmentItemId);
            }
        }

        try {
            // xml post structure
            $xml_post_array = array(
                'shipmentItems' => $missingLabels,
                "mergeLabels" => true
            );
            $xml_post_string = json_encode($xml_post_array);

            $authorization = "Authorization: ".$this->tToken." ".$this->aToken;
            $headers = array(
                "POST /api/Shipments/BookShipment HTTP/1.1",
                "Content-Type: application/json",
                "Content-length: ".strlen($xml_post_string),
                $authorization,
            );

            $ch = curl_init();
            curl_setopt_array($ch, array(
                CURLOPT_URL => "".$this->_apiUrl."",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => "".$xml_post_string."",
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_SSL_VERIFYPEER => false,

            ));

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if(empty($response)){
                $response = curl_error($ch);
                curl_close($ch);
                unset($ch);
                $this->_continue = false;
                $this->_messageManager->addError('Api response is empty');

            }else{
                $jsonDecoded = json_decode($response,true);
                curl_close($ch);
                unset($ch);

                $this->_response = $jsonDecoded;
            }

        }catch (Exception $e) {
            Mage::log('time 4.1: ' . time(). ' : '.$e->getMessage(),NULL,'shipmentexport.log');
            Mage::logException($e);

            // send email
            if($this->sendErrorMail == true){
                $this->_customHelper->sendErrorMail($e->getMessage(),$this->sendErrorMailTo);
            }
        }
    }

    public function createLabelsPdf()
    {
        $pdfDirPath = $this->_directory->getPath('var').'/pdf/';
        $pdfDirPathTemp = $this->_directory->getPath('var').'/pdf/tmp/';

        if (!file_exists($pdfDirPath)) {
            mkdir($pdfDirPath, 0777, true);
        }
        if (!file_exists($pdfDirPathTemp)) {
            mkdir($pdfDirPathTemp, 0777, true);
        }

        if(isset($this->_response['labelObject'])){
            $labelObject = $this->_response['labelObject'];
            $this->_pdf = NULL;
            return false;
        }
        elseif(isset($this->_response['labels']['0']['labelObject'])){
            $labelObject = $this->_response['labels']['0']['labelObject'];
        }else{
            $this->_pdf = NULL;
            return false;
        }
        $pdfDocs = array();


        //Decode pdf content
        $pdf_decoded = base64_decode($labelObject);
        //Write data back to pdf file
        $filename = $pdfDirPathTemp . implode($this->_allItemIds). '-file.pdf';
        $pdf = fopen($filename, 'w');
        fwrite($pdf, $pdf_decoded);
        //close output file
        fclose($pdf);

        $this->_pdf = $filename;

    }
}