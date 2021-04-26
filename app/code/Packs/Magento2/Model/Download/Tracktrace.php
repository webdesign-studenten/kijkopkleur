<?php

namespace Packs\Magento2\Model\Download;

use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Packs\Magento2\Model\ShipmentFactory as PacksShipmentsFactory;
use Packs\Magento2\Helper\Adminhtml\Data as CustomHelper;
use Magento\Sales\Model\Order\Shipment\TrackFactory as TrackFactory;

class Tracktrace extends AbstractModel
{
    protected $_apiUser;
    protected $_apiUserPassword;
    protected $_apiUrl;
    protected $_apiAuthUrl;
    protected $_continue = true;
    protected $_response = array();
    protected $_pdf;
    protected $tToken;
    protected $aToken;

    protected $_orderIds;
    protected $_packsShipmentsFactory;
    protected $_labelsData;
    protected $_allItemIds = array();
    protected $_customHelper;
    protected $_messageManager;
    protected $_directory;
    protected $_scopeConfig;
    protected $_trackFactory;

    public function __construct(
        Context $context,
        Registry $registry,
        ManagerInterface $messageManager,
        PacksShipmentsFactory $packsShipmentsFactory,
        DirectoryList $directory,
        ScopeConfigInterface $scopeConfig,
        CustomHelper $customHelper,
        TrackFactory $trackFactory
    ) {
        $this->_messageManager = $messageManager;
        $this->_packsShipmentsFactory = $packsShipmentsFactory;
        $this->_directory = $directory;
        $this->_customHelper = $customHelper;
        $this->_scopeConfig = $scopeConfig;
        $this->_trackFactory = $trackFactory;
        parent::__construct($context,$registry);
    }


    public function downloadTracktrace($orderIds)
    {
        return $this->preProcessingGetTracktrace($orderIds);
    }

    public function preProcessingGetTracktrace($orderIds){

        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $this->_apiUser = $this->_scopeConfig->getValue("packs/general/api_name", $storeScope);
        $this->_apiUserPassword = $this->_scopeConfig->getValue("packs/general/api_password", $storeScope);
        list($this->_apiUrl, $this->_apiAuthUrl) = $this->_customHelper->getApiUrls('getshipment');


        set_time_limit(720000);                 // script timeout: 15 mins.
        ini_set('display_errors', 1);
        error_reporting(E_ALL);
        ini_set('max_execution_time', 720000);
        ini_set("default_charset", "utf-8");
        ini_set("memory_limit", "-1");
        ini_set("display_errors", "on");
        ob_start();

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/packs_tracktrace.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);


        $this->_orderIds = $orderIds;

        return $this->ProcessingGetTracktrace();
    }

    public function ProcessingGetTracktrace(){

        $tokenData = $this->_customHelper->Authorize();
        $this->tToken = $tokenData['type'];
        $this->aToken = $tokenData['token'];

        $this->getPacksShipmentItems();

        if(isset($this->_labelsData)){
            foreach ($this->_labelsData as $shipment){
                $this->getShipmentFromApi($shipment['packs_shipment_id']);
            }
        }

        return $this->_response;
    }

    private function getPacksShipmentItems(){
        $resultFactory = $this->_packsShipmentsFactory->create();
        $labelsCollection = $resultFactory->getCollection();
        $labelsCollection->addFieldToFilter('magento_order_id', ['in' => $this->_orderIds]);
        $this->_labelsData =  $labelsCollection->getData();
        return;
    }

    public function getShipmentFromApi($data){

        try {
            // xml post structure
            $xml_post_array = array(
                'shipmentId' => $data


            );

            $xml_post_string = json_encode($xml_post_array);


            // PHP cURL  for https connection with auth
            $ch = curl_init();
            $url = $this->_apiUrl."/".$data;
            curl_setopt_array($ch, array(
                CURLOPT_URL => "".$url."",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    "Accept: application/json",
                    "Authorization: ".$this->tToken." ".$this->aToken,
                    "Content-Type: application/json",
                    "cache-control: no-cache",
                ),
                CURLOPT_SSL_VERIFYPEER => false,

            ));

            $response = curl_exec($ch);
            if(empty($response)){
                $response = curl_error($ch);
                curl_close($ch);
                unset($ch);
                $this->_continue = false;
                $this->_messageManager->addError('Api response is empty');
                $logger->info('DATA: ' .$data.' Response: '.$response);

            }else{

                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                if($httpCode == 404) {
                    $message = "HTTP Error 404. The requested resource is not found. URL";
                    $this->_messageManager->addError($message);
                    return;
                }
                if($httpCode == 400) {
                    $message = $response;
                    $this->_messageManager->addError($message);
                    $this->continue = false;
                    return;
                }
                if($httpCode == 503) {
                    $message = "HTTP Error 503. The api server is not available";
                    $this->_messageManager->addError($message);
                    $this->continue = false;
                    return;
                }

                $jsonDecoded = json_decode($response,true);
                curl_close($ch);
                unset($ch);

                array_push($this->_response,$jsonDecoded);
            }

        }catch (Exception $e) {
            $logger->info('Track&trace error: '.$e->getMessage());

            // send email
            if($this->sendErrorMail == true){
                $this->_customHelper->sendErrorMail($e->getMessage(),$this->sendErrorMailTo);
            }
        }
    }
}