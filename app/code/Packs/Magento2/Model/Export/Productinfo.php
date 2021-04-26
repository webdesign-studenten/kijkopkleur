<?php
namespace Packs\Magento2\Model\Export;

use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface as StoreManager;
use Magento\Framework\Message\ManagerInterface;
use Packs\Magento2\Helper\Adminhtml\Data as CustomHelper;

class Productinfo extends AbstractModel
{
    protected $_apiUser;
    protected $_apiUserPassword;
    protected $_apiUrl;
    protected $_apiAuthUrl;
    protected $oldTime;
    protected $orderIds;
    protected $postData;
    protected $response = array();
    protected $aToken;
    protected $tToken;
    protected $packageOptions;
    protected $_senderData;
    protected $_continue = true;

    protected $_scopeConfig;
    protected $_customHelper;
    protected $_storeManager;
    protected $_messageManager;

    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $scopeConfig,
        CustomHelper $helper,
        StoreManager $storeManager,
        ManagerInterface $messageManager,
        DateTime $dateTime
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_customHelper = $helper;
        $this->_storeManager = $storeManager;
        $this->_messageManager = $messageManager;
        $this->_dateTime = $dateTime;
        parent::__construct($context,$registry);
    }
    public function getProductInfo()
    {
        $this->preProcessing();
        $returnValues = array('fullresponse'=>$this->response, 'packageoptions'=>$this->packageOptions);
        return $this->response;
    }
    public function preProcessing(){

        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $this->_apiUser = $this->_scopeConfig->getValue("packs/general/api_name", $storeScope);
        $this->_apiUserPassword = $this->_scopeConfig->getValue("packs/general/api_password", $storeScope);
        list($this->_apiUrl, $this->_apiAuthUrl) = $this->_customHelper->getApiUrls('getproductinfo');

        $this->_continue = true;

        $this->setSenderData();


        set_time_limit(720000);                 // script timeout: 15 mins.
        ini_set('display_errors', 1);
        error_reporting(E_ALL);
        ini_set('max_execution_time', 720000);
        ini_set("default_charset", "utf-8");
        ini_set("memory_limit", "-1");
        ini_set("display_errors", "on");
        ob_start();

        $this->processing();
    }

    public function processing(){


        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/getproductinfo.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $this->_storeManager->setCurrentStore(0);

        $tokenData = $this->_customHelper->Authorize();
        if($tokenData){
            $this->tToken = $tokenData['type'];
            $this->aToken = $tokenData['token'];
        }else{
            $this->_messageManager->addError('Authorization failed. Check username and password');
            return false;
        }

        $logger->info('time 0: Authorize : ' . $this->getSeconds().' sec');

        $this->getDataFromApi();
        $logger->info('time 1: Get product info : ' . $this->getSeconds() . ' sec');

        if($this->_continue == true){
            $this->prepareResponse();
        }else{
            $logger->info('Empty response: ' . $this->getSeconds() . ' sec');
        }

    }

    public function getDataFromApi(){

        $handler = $this->_senderData['handler'];
        $network = $this->_senderData['network'];
        try {
            // xml post structure
            $xml_post_array = array(
               'Handler' => (string)$handler,
               'Network' => (string)$network
            );

            $xml_post_string = json_encode($xml_post_array);
            $authorization = "Authorization: ".$this->tToken." ".$this->aToken;
            $headers = array(
                "POST /api/Products/GetProductInfo HTTP/1.1",
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

            if(empty($response)){
                $response = curl_error($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                unset($ch);
                $this->_continue = false;
                $this->_messageManager->addError('Api response is empty');

            }else{
                $jsonDecoded = json_decode($response,true);
                curl_close($ch);
                unset($ch);

                $this->response = $jsonDecoded;
                $this->packageOptions = $this->response['products'];
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

    public function getSeconds(){
        if(!isset($this->oldTime)){
            $this->oldTime = time();
        }
        $newTime = time();
        $seconds = $newTime - $this->oldTime;
        $this->oldTime = $newTime;
        return $seconds;
    }

    public function setSenderData(){
        $storeScope = 'stores';
        $this->_senderData['name'] = $this->_scopeConfig->getValue("shipments/sender/sender_name", $storeScope);
        $this->_senderData['contact'] = $this->_scopeConfig->getValue("shipments/sender/sender_contact", $storeScope);
        $this->_senderData['street'] = $this->_scopeConfig->getValue("shipments/sender/sender_street", $storeScope);
        $this->_senderData['housenumber'] = $this->_scopeConfig->getValue("shipments/sender/sender_housenumber", $storeScope);
        $this->_senderData['postcode'] = $this->_scopeConfig->getValue("shipments/sender/sender_postcode", $storeScope);
        $this->_senderData['city'] = $this->_scopeConfig->getValue("shipments/sender/sender_city", $storeScope);
        $this->_senderData['country'] = $this->_scopeConfig->getValue("shipments/sender/sender_country", $storeScope);
        $this->_senderData['handler'] = $this->_scopeConfig->getValue("shipments/sender/sender_handler", $storeScope);
        $this->_senderData['network'] = $this->_scopeConfig->getValue("shipments/sender/sender_network", $storeScope);
        return;
    }
    private function prepareResponse(){
        $returnValues = array();
        foreach($this->response['products'] as $type){
            $optionNames = array();
            foreach ($type['shipmentItemSurcharges'] as $shipmentItemSurcharges){
                array_push($optionNames, $shipmentItemSurcharges['surcharge']);
            }
            $returnValues[$type['product']] = $optionNames;
        }
        $this->response = $returnValues;
    }
}