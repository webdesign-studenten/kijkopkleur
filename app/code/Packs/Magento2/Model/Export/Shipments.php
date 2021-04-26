<?php
namespace Packs\Magento2\Model\Export;

use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface as StoreManager;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Framework\Message\ManagerInterface;
use Packs\Magento2\Model\Shipment as PacksShipmentModel;
use Packs\Magento2\Model\Save\Shipments as ImportShipmentsModel;
use Packs\Magento2\Helper\Adminhtml\Data as CustomHelper;


class Shipments extends AbstractModel{

    protected $_apiUser;
    protected $_apiUserPassword;
    protected $_apiUrl;
    protected $_apiAuthUrl;
    protected $_oldTime;
    protected $_orderIds;
    protected $_postData;
    protected $_orders = array();
    protected $_ordersData = array();
    protected $_senderData = array();
    protected $_shipmentsData;
    protected $_responses = array();
    protected $_responseMessages = array();
    protected $tToken;
    protected $aToken;
    protected $_continue;
    protected $_dateTime;
    protected $_messageContainer = array();


    protected $_scopeConfig;
    protected $_customHelper;
    protected $_importShipmentsModel;
    protected $_storeManager;
    protected $_orderCollectionFactory;
    protected $_messageManager;
    protected $_packsShipmentModel;


    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $scopeConfig,
        CustomHelper $helper,
        ImportShipmentsModel $importShipmentsModel,
        PacksShipmentModel $packsShipmentModel,
        StoreManager $storeManager,
        CollectionFactory $orderCollectionFactory,
        ManagerInterface $messageManager,
        DateTime $dateTime
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_customHelper = $helper;
        $this->_importShipmentsModel = $importShipmentsModel;
        $this->_storeManager = $storeManager;
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->_messageManager = $messageManager;
        $this->_packsShipmentModel = $packsShipmentModel;
        $this->_dateTime = $dateTime;
        parent::__construct($context,$registry);
    }

    public function startExport($postData)
    {
        $this->setOrderIds($postData['order_ids']);
        $this->setPostData($postData);
        $this->preProcessingExport();
        return $this->_shipmentsData;
    }

    public function preProcessingExport(){
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;

        $this->_apiUser = $this->_scopeConfig->getValue("packs/general/api_name", $storeScope);
        $this->_apiUserPassword = $this->_scopeConfig->getValue("packs/general/api_password", $storeScope);
        list($this->_apiUrl, $this->_apiAuthUrl) = $this->_customHelper->getApiUrls('bookshipment');


        $this->_senderData['name'] = $this->_scopeConfig->getValue("shipments/sender/sender_name", $storeScope);
        $this->_senderData['contact'] = $this->_scopeConfig->getValue("shipments/sender/sender_contact", $storeScope);
        $this->_senderData['street'] = $this->_scopeConfig->getValue("shipments/sender/sender_street", $storeScope);
        $this->_senderData['housenumber'] = $this->_scopeConfig->getValue("shipments/sender/sender_housenumber", $storeScope);
        $this->_senderData['housenumberExt'] =  $this->_scopeConfig->getValue("shipments/sender/sender_housenumberExt", $storeScope);
        $this->_senderData['postcode'] = $this->_scopeConfig->getValue("shipments/sender/sender_postcode", $storeScope);
        $this->_senderData['city'] = $this->_scopeConfig->getValue("shipments/sender/sender_city", $storeScope);
        $this->_senderData['country'] = $this->_scopeConfig->getValue("shipments/sender/sender_country", $storeScope);
        $this->_senderData['handler'] = $this->_scopeConfig->getValue("shipments/sender/sender_handler", $storeScope);
        $this->_senderData['reference'] = $this->_scopeConfig->getValue("shipments/sender/sender_reference", $storeScope);
        $this->_senderData['mail'] = $this->_scopeConfig->getValue("shipments/sender/sender_mail", $storeScope);


        $this->_continue = true;
        set_time_limit(720000);                 // script timeout: 15 mins.
        ini_set('display_errors', 1);
        error_reporting(E_ALL);
        ini_set('max_execution_time', 720000);
        ini_set("default_charset", "utf-8");
        ini_set("memory_limit", "-1");
        ini_set("display_errors", "on");
        ob_start();

        $this->processingExport();
    }

    public function processingExport(){

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/shipmentexport.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $this->_storeManager->setCurrentStore(0);

        $logger->info('Export STARTED');

        $tokenData = $this->_customHelper->Authorize();
        $this->tToken = $tokenData['type'];
        $this->aToken = $tokenData['token'];
        $logger->info('time 0: Authorize : ' . $this->getSeconds().' sec');

        if($this->_continue == true){
            $this->getOrderDataFromMagento();
            $logger->info('time 1: DATA FROM MAGENTO : ' . $this->getSeconds().' sec');
        }

        if($this->_continue == true) {
            $this->sendShipmentDataToWebservices();
            $logger->info('time 2: DATA TO WEBSERVICES : ' . $this->getSeconds().' sec');
        }

        if($this->_continue == true) {
            $this->responseHandler();
            $logger->info('time 3: RESPONSE HANDLER : ' . $this->getSeconds().' sec');
        }

        if($this->_continue == true) {
            $this->_shipmentsData = $this->_importShipmentsModel->createMagentoShipments($this->_responseMessages['success']);
            $logger->info('time 4: CREATE SHIPMENTS IN MAGENTO : ' . $this->getSeconds().' sec');
        }

        if($this->_continue == true) {
            $this->savePacksShipmentData();
            $logger->info('time 4: SAVE PACKS SHIPMENTS IN DATABASE : ' . $this->getSeconds().' sec');
        }

        $this->messageHandler();
        $logger->info('time 5: MESSAGES RETURNED : ' . $this->getSeconds().' sec');
    }

    public function getOrderDataFromMagento(){

        if (!$this->_orders)
        {
            $this->_orders = $this->_orderCollectionFactory->create()->addAttributeToFilter('entity_id', ['in' => $this->_orderIds]);
        }

        foreach($this->_orders as $order){
            if($order->canShip())
            {
                $orderData = $order->getData();
                $orderData['shippingaddress'] = $order->getShippingAddress()->getData();

                if(count($order->getShippingAddress()->getStreet()) >= 2){
                    $streetFields = $order->getShippingAddress()->getStreet();
                    $orderData['shippingaddress']['street1'] = $streetFields[0];
                    $orderData['shippingaddress']['street2'] = $streetFields[1];
                    if(isset($streetFields[2])){
                        $orderData['shippingaddress']['street3'] = $streetFields[2];
                    }
                    else{
                        $orderData['shippingaddress']['street3'] = '';
                    }
                }else{
                    $tmp = $order->getShippingAddress()->getStreet();
                    list($orderData['shippingaddress']['street1'],
                         $orderData['shippingaddress']['street2'])
                        = $this->_explodeAddress($tmp[0]);
                    $orderData['shippingaddress']['street3'] = '';

                }
                $this->_ordersData[] = $orderData;
            }else{
                $message = 'Order already shipped';

                $this->_responseMessages['errors'][$order->getIncrementId()] = $message;
            }
        }
        return;
    }

    public function sendShipmentDataToWebservices(){

        foreach($this->_ordersData as $order){
            try {
                // xml post structure

                $xml_post_array = array(
                    'handler'=> $this->_senderData['handler'],
                    'network'=> 'NextDay',
                    'loadDate'=> $this->getLoadDate($order),
                    'deliveryDate'=> $this->getDeliveryDate($order),
                    'loadAddress'=> array(
                        'country'=> $this->_senderData['country'],
                        'name'=> $this->_senderData['name'],
                        'street'=> $this->_senderData['street'],
                        'number'=> $this->_senderData['housenumber'],
                        'numberExt'=> $this->_senderData['housenumberExt'],
                        'location'=> '',
                        'zip'=> $this->_senderData['postcode'],
                        'place'=> $this->_senderData['city'],
                        'reference'=> $this->_senderData['reference'],
                        'mail' => $this->_senderData['mail']
                    ),
                    'deliveryAddress'=> array(
                        'country'=> $order['shippingaddress']['country_id'],
                        'name'=> $order['shippingaddress']['firstname'] .' '. $order['shippingaddress']['lastname'],
                        'street'=> $order['shippingaddress']['street1'],
                        'number'=> $order['shippingaddress']['street2'],
                        'numberExt'=> $order['shippingaddress']['street3'],
                        'location'=> 'Verdieping',// ??
                        'zip'=> $order['shippingaddress']['postcode'],
                        'place'=> $order['shippingaddress']['city'],
                        'reference'=> $this->getReferentie($order),
                        'mail' => $order['shippingaddress']['email'],
                        'phone' => $order['shippingaddress']['telephone']
                    ),
                    'surcharges'=> array(),
                    'shipmentItems' => $this->getShipmentItems($order),
                );

                $xml_post_string = json_encode($xml_post_array);
                $authorization = "Authorization: ".$this->tToken." ".$this->aToken;
                $headers = array(
                    "POST /api/Shipments/BookShipment HTTP/1.1",
                    "Content-Type: application/json",
                    "Content-length: ".strlen($xml_post_string),
                    $authorization,
                );

                // PHP cURL  for https connection with auth
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_URL, $this->_apiUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
                curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string); // the api request
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                $response = curl_exec($ch);

                if(empty($response)){
                    $response = curl_error($ch);
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    curl_close($ch);
                    unset($ch);
                    $this->_continue = false;
                    $this->_messageManager->addError('Api response is empty');
                    break;

                }else{

                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    if($httpCode == 200) {
                        curl_close($ch);
                        unset($ch);
                        $this->_responseMessages['success'][$order['increment_id']] = $response;
                    }
                    if($httpCode == 400) {
                        curl_close($ch);
                        unset($ch);
                        $this->_responseMessages['errors'][$order['increment_id']] = $response;
                    }
                    if($httpCode == 404) {
                        $message = "HTTP Error 404. The requested resource is not found. URL";
                        $this->_responseMessages['errors'][$order['increment_id']] = $message;
                    }
                    if($httpCode == 503) {
                        $message = "HTTP Error 503. The api server is not available";
                        $this->_responseMessages['errors'][$order['increment_id']] = $message;
                        $this->_continue = false;
                    }
                }

            }catch (Exception $e) {
                Mage::log('time 4.1: ' . time(). ' : '.$e->getMessage(),NULL,'shipmentexport.log');
                Mage::logException($e);

                // send email
                if($this->sendErrorMail == true){
                    $this->helper->sendErrorMail($e->getMessage(),$this->sendErrorMailTo);
                }
            }
        }
    }

    public function getShipmentItems($order){
        $shipmentItems = array();
        $colliQty = (int)$this->getCollie($order);

        for($i=0; $i<$colliQty; $i++){
            $surcharges = $this->getShipmentItemSurcharges($order);
            if($surcharges){
                $shipmentItem = array(
                    'product' => $this->getSeal($order),
                    'weight' => $this->getTotalWeight($order,$colliQty),
                    'length'=> 1,
                    'height'=> 1,
                    'width'=> 1,
                    'labelText'=> 'collo'.$i,
                    'surcharges'=> array($surcharges),
                );
            }else{
                $shipmentItem = array(
                    'product' => $this->getSeal($order),
                    'weight' => $this->getTotalWeight($order,$colliQty),
                    'length'=> 1,
                    'height'=> 1,
                    'width'=> 1,
                    'labelText'=> 'collo'.$i,
                );
            }

            array_push($shipmentItems,$shipmentItem);
        }

        return $shipmentItems;
    }

    private function getShipmentItemSurcharges($order){
        if($this->getAllowance($order)) {
            $surcharge = array('surcharge'=> $this->getAllowance($order));
        }else{
            $surcharge = false;
        }
        return $surcharge;
    }

    public function responseHandler(){
        foreach($this->_responseMessages as $type => $responses){
            if($type == 'errors'){
                foreach($responses as $incrementId => $message){
                    $this->_messageContainer['errors'][$incrementId] = $message;
                    unset($this->_responseMessages['errors'][$incrementId]);
                }
            }
            if(!array_key_exists('success',$this->_responseMessages)){
                $this->_continue = false;
            }
        }
        return;
    }

    private function messageHandler(){
        foreach($this->_messageContainer as $type => $responses){
            if($type == 'errors') {
                foreach($responses as $incrementId => $message){
                    $this->_messageManager->addErrorMessage($incrementId . ' : ' . $message);
                }
            }
            if($type == 'success') {
                foreach($responses as $incrementId => $message) {
                    $this->_messageManager->addSuccessMessage($incrementId . ' : Exported successfully');
                }
            }
        }
    }

    public function savePacksShipmentData(){

        foreach($this->_shipmentsData as $shipment){
            if(isset($shipment['packs'])){
                $packsData = (array)$shipment['packs'];
            }else{
                $message = 'Shipment does not contain any data';
                $this->_messageContainer['error'][$shipment['order_id']] = $message;
                return false;
            }
            $shipmentItemIds = array();
            foreach((array)$packsData['shipmentItems'] as $packsShipmentItem){
                array_push($shipmentItemIds,$packsShipmentItem->shipmentItemId);
            }
            if(isset($this->_postData[$shipment['order_increment_id'].'-allowance'])){
                $surcharge = $this->_postData[$shipment['order_increment_id'].'-allowance'];
            }else{
                $surcharge = '';
            }

            $saveData = array(
                'magento_order_id' => $shipment['order_id'],
                'magento_shipment_id' => $shipment['entity_id'],
                'packs_shipment_id' => $packsData['shipmentId'],
                'packs_shipment_item_ids' => implode(',',$shipmentItemIds),
                'created_at' => $this->_dateTime->gmtDate('Y-m-d H:i:s'),
                'load_date' => $packsData['loadDate'],
                'delivery_date' => $packsData['deliveryDate'],
                'confirm_status' => 1,
                'confirm_date' => $this->_dateTime->gmtDate('Y-m-d H:i:s'),
                'shipment_type' => $surcharge,
                'seal_type' => $this->_postData[$shipment['order_increment_id'].'-seal'],
                'collie' => $this->_postData[$shipment['order_increment_id'].'-collie'],
                'weight' => $this->_postData[$shipment['order_increment_id'].'-weight'],
                'reference' => $this->_postData[$shipment['order_increment_id'].'-reference'],
            );
            $this->_packsShipmentModel->setData($saveData);
            $this->_packsShipmentModel->save();

            $message = 'Exported successfully';
            $this->_messageContainer['success'][$shipment['order_id']] = $message;

        }
        return;
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

    public function setOrderIds($orderIds)
    {
        $this->_orderIds = $orderIds;
    }

    public function setPostData($postData)
    {
        $this->_postData = $postData;
    }

    public function getSeal($order){
        if(isset($this->_postData[$order['increment_id'].'-seal'])){
            $seal = $this->_postData[$order['increment_id'].'-seal'];
        }else{
            $seal = '';
        }

        return $seal;
    }

    public function getLoadDate($order){
        if(isset($this->_postData[$order['increment_id'].'-loaddate'])){
            $loadDate = $this->_postData[$order['increment_id'].'-loaddate'];
        }else{
            $loadDate = '';
        }

        return $loadDate;
    }

    public function getDeliveryDate($order){
        if(isset($this->_postData[$order['increment_id'].'-deliverydate'])){
            $deliveryDate = $this->_postData[$order['increment_id'].'-deliverydate'];
        }else{
            $deliveryDate = '';
        }

        return $deliveryDate;

    }

    public function getReferentie($order){
        if(isset($this->_postData[$order['increment_id'].'-reference'])){
            $reference = $this->_postData[$order['increment_id'].'-reference'];
        }else{
            $reference = '';
        }

        return $reference;
    }

    public function getCollie($order){
        if(isset($this->_postData[$order['increment_id'].'-collie'])){
            $collie = $this->_postData[$order['increment_id'].'-collie'];
        }else{
            $collie = '';
        }

        return $collie;
    }

    public function getTotalWeight($order,$colliQty){
        if(isset($this->_postData[$order['increment_id'].'-weight'])){
            $weight = (int)$this->_postData[$order['increment_id'].'-weight']/$colliQty;
        }else{
            $weight = '';
        }

        return $weight;
    }

    public function getAllowance($order){
        if(isset($htis->_postData[$order['increment_id'].'allowance'])){
            $allowance = $this->_postData[$order['increment_id'].'-allowance'];
            return $allowance;
        }else{
            return false;
        }
    }

    /*
    ** Explodes a given address to
    ** a streenname and streetnumber.
    **
    ** For example:
    ** $result = explodeAddress('streetname 123');
    ** ['streetname', '123']
    ** Or if no steetnumber given:
    ** $result = explodeAddress('streetname');
    ** ['streetname', '']
    ** $result = explodeAddress('streetname streetname');
    ** ['streetname streetname', '']
    */
    private function _explodeAddress( $_input ){

        $regex = '/^((?:[^\s]+\s+)+)([^\s]+)$/';
        preg_match( $regex, $_input, $match );
        if( !$match ) {
            return [$_input, ''];
        }

        list(, $street, $streetnr) = $match;
        $street = trim($street);
        $streetnr = trim($streetnr);

        /* Fixing test cases:
        ** - "De Dompelaar 1 B"
        ** - "Saturnusstraat 60 - 75" */
        if( preg_match('/[0-9]+[\-\s]*$/', $street, $match)
            /* If $street not ends with a number
            ** and $streetnr not begins with a number. */
            && !(preg_match('/[0-9]$/', $street)
                && preg_match('/^[0-9]/', $streetnr)) ) {
            $n = strlen($street) - strlen($match[0]);
            if( $n >= 1 ) {
                $street = substr($street, 0, $n);
                $streetnr = $match[0] . ' ' . $streetnr;
            }
        }
        /* Fixing test cases:
        ** - "glaslaan 2, gebouw SWA 71"
        ** - "straat 32 verdieping 2" */
        else if( preg_match('/[^0-9]([0-9]+[^0-9]+)$/', $street, $match) ){
            $n = strlen($street) - strlen($match[1]);
            if( $n >= 1 ) {
                $street = substr($street, 0, $n);
                $streetnr = $match[1] . ' ' . $streetnr;
            }
        }
        /* Fixing test cases:
        ** - "1, rue de l'eglise" */
        else if( preg_match('/^([0-9]+\s*),([\s\S]+)/', $_input, $match) ) {
            $street = $match[2];
            $streetnr = $match[1];
        }
        /* Fixing test cases:
        ** - "3-koningenstraat, 21 13b" */
        else if( preg_match('/,\s*([0-9]+)$/', $street, $match) ) {
            $n = strlen($street) - strlen($match[1]);
            if( $n >= 1 ) {
                $street = substr($street, 0, $n);
                $streetnr = $match[1] . ' ' . $streetnr;
            }
        }

        /* If street number contains no number then
        ** "$street = $_input" and streetnr is empty. */
        if( !preg_match('/[0-9]/', $streetnr) ) {
            $street = $_input;
            $streetnr = '';
        }

        $street = rtrim(trim($street), ',');
        $streetnr = trim($streetnr);
        return [$street, $streetnr];

    } /* end function explodeAddress() */

}
