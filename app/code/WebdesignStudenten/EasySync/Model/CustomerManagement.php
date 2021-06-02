<?php
/**
 * Admin can sync customer, products, sales, cart, newsletter subscribers, wishlist etc.
 * Copyright (C) 2019  
 * 
 * This file is part of WebdesignStudenten/EasySync.
 * 
 * WebdesignStudenten/EasySync is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace WebdesignStudenten\EasySync\Model;

class CustomerManagement implements \WebdesignStudenten\EasySync\Api\CustomerManagementInterface
{
    protected $resultFactory;
    
    /**
     * Customer registry.
     *
     * @var \Magento\Customer\Model\CustomerRegistry
     */
    protected $_customerRegistry;
 
    public function __construct(
        \Magento\Framework\Json\Helper\Data $resultFactory,
        \Magento\Customer\Model\CustomerRegistry $customerRegistry
    ) {
        $this->resultFactory   = $resultFactory;
        $this->_customerRegistry   = $customerRegistry;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getCustomer($customerId)
    {
        
        //$this->_request->setHeaders($this->_httpHeaders);
        $customerData = array();
        $customerModel = $this->_customerRegistry->retrieve($customerId);
        $customerAddress = array();
        
        foreach ($customerModel->getAddresses() as $address)
        {
            $customerAddress[] = $address->getData();
        }
        $customerData['customerData'] = $customerModel->getData();
        $customerData['customerAddress'] = $customerAddress;
        return json_encode($customerData);
//        return $customerModel->getCustomDataModel();
//        return get_class($customerModel); //->getDataModel();
//        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);  //create Json type return object
//    	$resultJson->setData($customer->getData());  // array value set in Json Result Data set
//        return $this->resultFactory->jsonEncode($customer->getData());
//        $response = $this->_client->create();
//        $this->_client->getHeaders()->addHeaderLine( 'Content-Type', 'application/json' );
        //return $this->_client->getHeaders()->get('Content-Encoding');
     // $this->_client->setContent(json_encode($customer->getData()));
//return $this->_client->getBody();
//         $xx = json_encode($customer->getData());
//         return  $xx;
//        $customer_access_token = "mk44h87rrsbgvi3lf8htg8u795vuxrxx";
//        
//        $this->_httpHeaders->addHeaders([
//            'Authorization' => 'Bearer ' . $customer_access_token,
//            'Accept' => 'application/json',
//            'Content-Type' => 'application/json',
//        ]);
//
//        $this->_request->setHeaders($this->_httpHeaders);
//        $apiUrl = '';
//        $apiUrl = $this->_storeManager->getStore()->getUrl('rest/V1/products?fields=items[sku,name]&searchCriteria[pageSize]=10');
//        
//        $this->_request->setUri($apiUrl);
//        
//        $response = $this->_client->send($this->_request);
//        return $response->getBody();
    }
}
