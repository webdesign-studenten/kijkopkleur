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

namespace WebdesignStudenten\EasySync\Cron;

class ServerDataSync
{

    protected $logger;
    protected $helper;
    /**
     * @var CustomerFactory
     */
    private $customerRegistry;
    private $customerFact;
    private $customerFactory;
    private $customer;
    /**
     * @var Magento\Customer\Model\AddressFactory
     */
    protected $addressDataFactory;


    /**
     * Constructor
     *
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \WebdesignStudenten\EasySync\Helper\Data $helper,
        \WebdesignStudenten\EasySync\Api\Data\CustomerInterface $customer,
        \Magento\Customer\Model\CustomerRegistry $customerRegistry,
        \Magento\Customer\Model\Customer $customerFact,
        \Magento\Customer\Model\ResourceModel\CustomerRepository $customerFactory,
        \Magento\Customer\Model\AddressFactory $addressDataFactory
    ) {
        $this->logger = $logger;
        $this->helper = $helper;
        $this->customer = $customer;
        $this->customerRegistry = $customerRegistry;
        $this->customerFact = $customerFact;
        $this->customerFactory = $customerFactory;
        $this->addressDataFactory = $addressDataFactory;
    }
    
    /**
     * @param string     $email
     * @param null $websiteId
     *
     * @return bool|\Magento\Customer\Model\Customer
     */
    public function customerExists($email, $websiteId = null)
    {
        $customer = $this->customerFact;
        if ($websiteId) {
            $customer->setWebsiteId($websiteId);
        }
        $customer->loadByEmail($email);
        if ($customer->getId()) {
            return $customer;
        }

        return false;
    }

    /**
     * Execute the cron
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->helper->isEnabled()) return;
        $updatedCustomerAPIUrl = 'rest/V1/webdesignstudenten-easysync/updatedcustomer';
        $sXML = $this->helper->getApiData($updatedCustomerAPIUrl);
        foreach($sXML->item as $oEntry){
            $customerAPIUrl = 'rest/V1/webdesignstudenten-easysync/customer/' . $oEntry->dataID;
            $sXML = $this->helper->getApiData($customerAPIUrl);
            $customerData = json_decode($sXML);
            $customerDataAddress = json_decode(json_encode($customerData), true);
            $customerDataArray = $customerDataAddress['customerData'];
            $customerAddressArray = $customerDataAddress['customerAddress'];
            $custId = '';
            if ($customerModel = $this->customerExists($customerDataArray['email'], $customerDataArray['website_id'])) {
                unset($customerDataArray['entity_id']);
                foreach ($customerDataArray as $key => $value) {
                    $customerModel->setData($key, $value);
                }
//                $this->customerFactory->save($customerModel, $customerDataArray['password_hash']);
                $customerModel->save();
                $custId = $customerModel->getId();
            } else {
                foreach ($customerDataArray as $key => $value) {
                    $this->customer->setData($key, $value);
                }
                $this->customerFactory->save($this->customer, $customerDataArray['password_hash']);
                $custId = $this->customer->getId();
            }
            // Saving customer addresses
            
            foreach ($customerAddressArray as $customerAddress) {
                $address = $this->addressDataFactory->create();
                foreach ($customerAddress as $key => $value) {
                    if ($key == 'attributes') continue;
                    if ($key == 'customer_id') $value = $custId;
                    if ($key == 'parent_id') $value = $custId;
                    $address->setData($key, $value);
                }
//                print_r($address->getData()); die;
                $address->save();
            }
            $this->helper->setApiData($updatedCustomerAPIUrl . '/' . $oEntry->data_sync_id);
        }
    }
}
