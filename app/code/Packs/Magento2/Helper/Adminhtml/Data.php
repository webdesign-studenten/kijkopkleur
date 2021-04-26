<?php

namespace Packs\Magento2\Helper\Adminhtml;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Session\SessionManagerInterface as CoreSession;
use Magento\Framework\App\Config\ScopeConfigInterface as ScopeConfigInterface;

class Data extends AbstractHelper
{
    protected $_coreSession;
    protected $_scopeConfig;

    public function __construct(
        CoreSession $coreSession, ScopeConfigInterface $scopeConfig
    )
    {
        $this->_scopeConfig = $scopeConfig;
        $this->_coreSession = $coreSession;
    }

    public function setOrderIds($ids)
    {
        $this->_coreSession->start();
        $this->_coreSession->setPacksOrderIds($ids);
        return;
    }

    public function getOrderIds()
    {
        $this->_coreSession->start();
        $orderIds = $this->_coreSession->getPacksOrderIds();
        return $orderIds;
    }

    public function setApiToken($token){
        $this->_coreSession->start();
        $this->_coreSession->setApiToken($token);
        return;
    }

    public function getApiToken(){
        $this->_coreSession->start();
        $apiToken = $this->_coreSession->getApiToken();
        return $apiToken;
    }

    public function Authorize()
    {
        $storeScope = 'stores';
        $this->_apiUser = $this->_scopeConfig->getValue("packs/general/api_name", $storeScope);
        $this->_apiUserPassword = $this->_scopeConfig->getValue("packs/general/api_password", $storeScope);
        list(, $this->_apiAuthUrl) = $this->getApiUrls('');
            $postString = 'client_id=PacksOnlineApp&client_secret=secret&grant_type=password&scope=PacksOnlineAPI&username='.$this->_apiUser.'&password='.$this->_apiUserPassword;

            // PHP cURL  for https connection with auth
            $ch = curl_init();
            curl_setopt_array($ch, array(
                CURLOPT_URL => "".$this->_apiAuthUrl."",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => "".$postString."",
                CURLOPT_HTTPHEADER => array(
                    "Cache-Control: no-cache",
                    "Content-Type: application/x-www-form-urlencoded",
                ),
                CURLOPT_SSL_VERIFYPEER => false,
            ));

            $result = curl_exec($ch);

            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            unset($ch);

            $json = json_decode($result);
        if(isset($json->access_token)){
            $tokenData = array('type'=>$json->token_type, 'token'=>$json->access_token);
            return $tokenData;
        }else{
            return false;
        }
    }

    /*
    **
    */
    public function getApiUrls( $modename ){

        $storeScope = 'stores';
        $mode = $this->_scopeConfig->getValue("packs/general/mode", $storeScope);

        if( 'live' == $mode ) {
            $ApiBaseUrl = 'https://packsonlineapp.packs.nl/';
            $ApiAuthUrl = 'https://identityserver.packs.nl/connect/token';
        } else {
            $ApiBaseUrl = 'https://packsonlineapp-tst.packs.nl/';
            $ApiAuthUrl = 'https://identityserver-tst.packs.nl/connect/token';
        }

        switch( $modename ) {
            case 'bookshipment':
                $ApiBaseUrl .= 'api/Shipments/BookShipment';
                break;
            case 'getproductinfo':
                $ApiBaseUrl .= 'api/Products/GetProductInfo';
                break;
            case 'getlabels':
                $ApiBaseUrl .= 'api/Shipments/GetLabels';
                break;
            case 'getshipment':
                $ApiBaseUrl .= 'api/Shipments/getShipment';
                break;
        }

        return [$ApiBaseUrl, $ApiAuthUrl];

    } // end function getApiUrls()

}