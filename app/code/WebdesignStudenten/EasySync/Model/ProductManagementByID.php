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

class ProductManagementByID implements \WebdesignStudenten\EasySync\Api\ProductManagementByIDInterface
{
    
    protected $_storeManager;
    protected $_httpHeaders;
    protected $_request;
    protected $_client;
 
    public function __construct(  
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Zend\Http\Headers $httpHeaders,
        \Zend\Http\Request $request,
        \Zend\Http\Client $client
    )
    {        
        $this->_storeManager = $storeManager;
        $this->_httpHeaders = $httpHeaders;
        $this->_request = $request;
        $this->_client = $client;
    }
    /**
     * {@inheritdoc}
     */
    public function getProduct($prodID)
    {
        
        $customer_access_token = 'uwa2p5bnp9wyiwgk6gkqyq1s50vj3lsq';
        
        $this->_httpHeaders->addHeaders([
            'Authorization' => 'Bearer ' . $customer_access_token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ]);

        $this->_request->setHeaders($this->_httpHeaders);
        $apiUrl = '';
        if ($prodID == null) {
            $apiUrl = $this->_storeManager->getStore()->getUrl('rest/V1/products?fields=items[sku,name]&searchCriteria[pageSize]=1000');
        } else {
            $apiUrl = $this->_storeManager->getStore()->getUrl('rest/V1/products?searchCriteria[filterGroups][0][filters][0][field]=entity_id&searchCriteria[filterGroups][0][filters][0][value]='. $prodID);
        }
        $this->_request->setUri($apiUrl);
        
        $response = $this->_client->send($this->_request);
        return $response->getBody();
    }
}
