<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace WebdesignStudenten\EasySync\Model;


class Customer extends \Magento\Customer\Model\Customer
{
    /**
     * Retrieve customer model with customer data
     *
     * @return \WebdesignStudenten\EasySync\Api\Data\CustomerInterface
     */
    public function getCustomDataModel()
    {
        $customerData = $this->getData();
        $addressesData = [];
        /** @var \Magento\Customer\Model\Address $address */
        foreach ($this->getAddresses() as $address) {
            $addressesData[] = $address->getDataModel();
        }
        $customerDataObject = $this->customerDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $customerDataObject,
            $customerData,
            \WebdesignStudenten\EasySync\Api\Data\CustomerInterface::class
        );
        $customerDataObject->setAddresses($addressesData)
            ->setId($this->getId());
        return $customerDataObject;
    }
}
