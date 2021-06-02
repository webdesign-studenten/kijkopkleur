<?php


namespace WebdesignStudenten\EasySync\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    const API_SERVER_URL = 'easysync/syncserver/server_url';
    const DATA_MODE = 'easysync/syncserver/data_mode';
    const ENABLE = 'easysync/syncserver/enable';
    const NEXT_RUN = 'easysync/syncserver/sync_next_run';
    const RUN_INTERVAL = 'easysync/syncserver/sync_run_interval';
    protected $scopeConfig;
    protected $configFactory;
    /**
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Config\Model\Config\Factory $configFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->configFactory = $configFactory;
        parent::__construct($context);
    }
    public function getApiServerUrl()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(static::API_SERVER_URL, $storeScope);
    }
    public function getDataMode()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(static::DATA_MODE, $storeScope);
    }

    public function isEnabled()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return ($this->isReady() && $this->scopeConfig->getValue(static::ENABLE, $storeScope));
    }
//    public function getApiKey()
//    {
//        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
//        return $this->scopeConfig->getValue(static::API_KEY, $storeScope);
//    }
    public function isReady()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $interval = $this->scopeConfig->getValue(static::RUN_INTERVAL, $storeScope);
        $nextRun = $this->scopeConfig->getValue(static::NEXT_RUN, $storeScope);
        $minuteInterval = intval(1440 / $interval);
        // echo '$nextRun----' . date('m-d-Y H:i:s', $nextRun);
        // echo '--NOW----' . date('m-d-Y H:i:s', time());
        // echo '---minuteInterval+----' . $minuteInterval;
        if ($nextRun > time()) {
            return false;
        }

        $configData = [
            'section' => 'easysync',
            'website' => null,
            'store'   => null,
            'groups'  => [
                'syncserver' => [
                    'fields' => [
                        'sync_next_run' => [
                            'value' => strtotime("+". $minuteInterval ." minutes"),
                        ],
                    ],
                ],
            ],
        ];

        $configModel = $this->configFactory->create(['data' => $configData]);
        $configModel->save();
        return true;
    }
    
    public function getApiData($curlEndPoint)
    {
        $path = $this->getApiServerUrl() . $curlEndPoint;
        
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $path);
        curl_setopt($ch, CURLOPT_FAILONERROR,1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("accept: application/xml","content-type: text/xml"));
        $retValue = curl_exec($ch);
        $err = curl_error($ch);
        if ($err) {
            return false;
        }
        curl_close($ch);
        return simplexml_load_string($retValue);
    }
    
    public function setApiData($curlEndPoint)
    {
        $url = $this->getApiServerUrl() . $curlEndPoint;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");

        $response = curl_exec($ch);

        if (!$response) 
        {
            return false;
        }
    }

    public function getRecursiveArrayDiff($a1, $a2) { 
        $r = array(); 
        foreach ($a1 as $k => $v) {
            if (array_key_exists($k, $a2)) { 
                if (is_array($v)) { 
                    $rad = $this->getRecursiveArrayDiff($v, $a2[$k]); 
                    if (count($rad)) { $r[$k] = $rad; } 
                } else { 
                    if ($v != $a2[$k]) { 
                        $r[$k] = $v; 
                    }
                }
            } else { 
                $r[$k] = $v; 
            } 
        } 
        return $r; 
    }
    
    
}
