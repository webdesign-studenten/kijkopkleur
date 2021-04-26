<?php

/**

*

* Do not edit or add to this file if you wish to upgrade the module to newer

* versions in the future. If you wish to customize the module for your

* needs please contact us to https://www.milople.com/contact-us.html

*

* @category    Ecommerce

* @package     Milople_VATExempt

* @copyright   Copyright (c) 2017 Milople Technologies Pvt. Ltd. All Rights Reserved.

* @url         https://www.milople.com/magento-extensions/vat-exempt-m2.html

*

**/

namespace Milople\Vatexempt\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper

{
	const XML_PATH_Milople_Vatexempt = '/Milople/vtx/data/';
    protected $scopeConfig;



    

    public function __construct(

    \Magento\Framework\App\RequestInterface $httpRequest, 

    \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, 

    \Psr\Log\LoggerInterface $logger, 

		\Milople\Vatexempt\Model\Conditions $conditions,

		\Milople\Vatexempt\Model\Details $detailsModel,

    \Magento\Store\Model\StoreManagerInterface $storeManager

    )

    {

        $this -> scopeConfig = $scopeConfig;

        $this -> storeManager = $storeManager;

        $this -> logger = $logger;

				$this->detailsModel=$detailsModel;

			  $this -> conditions =$conditions;

        $this-> request = $httpRequest;

    }

    

    public function getDomain()

    {

        $domain =$this->request->getServer('SERVER_NAME');

        $temp = explode('.', $domain);

        $exceptions = array('co.uk', 'com.au', 'com.hk', 'co.nz', 'co.in', 'com.sg');

        $count = count($temp);

        if ($count === 1) {

            return $domain;

        }

        $last = $temp[($count - 2)] . '.' . $temp[($count - 1)];

        if (in_array($last, $exceptions)) {

            $new_domain = $temp[($count - 3)] . '.' . $temp[($count - 2)] . '.' . $temp[($count - 1)];

        } else {

            $new_domain = $temp[($count - 2)] . '.' . $temp[($count - 1)];

        }



        return $new_domain;

    }

    public function checkEntry($domain, $serial)

    {

        $key = sha1(base64_decode('TTJWYXRFeGVtcHQ='));

        if (sha1($key . $domain) == $serial) {

            return true;

        }

        return false;

    }



    public function canRun($temp = '')

    {
        return true;


	    	$domain =$this->request->getServer('SERVER_NAME');

        if ($domain == "localhost" || $domain == "127.0.0.1") {

            return true;

        }



        if ($temp == '') {

            $temp = $this ->scopeConfig->getValue('vatexempt/licenseOptions/vatexempt_serialkey', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        }

        $url = $this -> storeManager -> getStore() -> getBaseUrl();

        $parsedUrl = parse_url($url);

        $host = explode('.', $parsedUrl['host']);

        $subdomains = array_slice($host, 0, count($host) - 2);

        if (sizeof($subdomains) && ($subdomains[0] == 'test' || $subdomains[0] == 'demo' || $subdomains[0] == 'dev')) {

            return true;

        }

        $original = $this -> checkEntry($this->request->getServer('SERVER_NAME'), $temp);

        $wildcard = $this -> checkEntry($this -> getDomain(), $temp);

        if (!$original && !$wildcard) {

            return false;

        }

        return true;

    }

    public function getMessage()

    {

        return base64_decode('PGRpdj5MaWNlbnNlIG9mIDxiPk1pbG9wbGUgVkFUIEV4ZW1wdDwvYj4gZXh0ZW5zaW9uIGhhcyBiZWVuIHZpb2xhdGVkLiBUbyBnZXQgc2VyaWFsIGtleSBwbGVhc2UgY29udGFjdCB1cyBvbiA8Yj5odHRwczovL3d3dy5taWxvcGxlLmNvbS9tYWdlbnRvLWV4dGVuc2lvbnMvY29udGFjdHMvPC9iPi48L2Rpdj4=');

    }

    public function getAdminMessage()

    {

        return base64_decode('PGRpdj5MaWNlbnNlIG9mIDxiPk1pbG9wbGUgVkFUIEV4ZW1wdDwvYj4gZXh0ZW5zaW9uIGhhcyBiZWVuIHZpb2xhdGVkLiBUbyBnZXQgc2VyaWFsIGtleSBwbGVhc2UgY29udGFjdCB1cyBvbiA8Yj5odHRwczovL3d3dy5taWxvcGxlLmNvbS9tYWdlbnRvLWV4dGVuc2lvbnMvY29udGFjdHMvPC9iPi48L2Rpdj4=');

    }

	

	  #General Function for generating Configuration

    public function getConfig($config_path)

    {

        return $this->scopeConfig->getValue(

            $config_path,

            \Magento\Store\Model\ScopeInterface::SCOPE_STORE

        );

    }

	  # Return the name of condition of Medical Condition

	  public function getConditionName($conditionId)

		{

			$model=$this -> conditions;

			$collection=$model->getCollection() ->addFieldToFilter('condition_id', array('eq' => $conditionId));

      return $collection->getFirstItem()->getConditionName();                        

		}

	  # Return the date of condition of Medical Condition

	  public function getConditionDate($conditionId)

		{

			$model=$this -> conditions;

			$collection=$model->getCollection() ->addFieldToFilter('condition_id', array('eq' => $conditionId));

      return $collection->getFirstItem()->getPublishedAt();                        

		}

		# Get VAT order detail inside the order 

	  public function getVatOrderDetail($orderId)

		{

				$model=$this->detailsModel;

				$collection=$model->getCollection() ->addFieldToFilter('order_id', array('eq' => $orderId));

			  return $collection;

		}

	

}

