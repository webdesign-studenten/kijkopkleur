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

use Magento\Quote\Model\Quote\Address;

class QuoteDataSync
{

    protected $helper;

    /**
    * @param Magento\Quote\Model\Quote $quote
    */
    public function __construct(
        \WebdesignStudenten\EasySync\Helper\Data $helper,
        \Magento\Quote\Model\QuoteFactory $quote,
        \Magento\Quote\Api\Data\AddressInterface $address,
        \Magento\Quote\Model\QuoteRepository $quoteRespository,
        \Magento\Quote\Model\Quote\ItemFactory $quoteItemFactory
    ) {
        $this->helper = $helper;
        $this->quote = $quote;
        $this->address = $address;
        $this->quoteRespository = $quoteRespository;
        $this->quoteItemFactory = $quoteItemFactory;
    }

    /**
     * Execute the cron
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->helper->isEnabled()) return;
        $updatedquoteAPIUrl = 'rest/V1/webdesignstudenten-easysync/updatedQuote';
        $sXML = $this->helper->getApiData($updatedquoteAPIUrl);
        if (empty($sXML->item)) return;

        foreach ($sXML->item as $oEntry) {
            $quoteAPIUrl = 'rest/V1/webdesignstudenten-easysync/quote/' . $oEntry->dataID;
            $sXML = $this->helper->getApiData($quoteAPIUrl);
            $quoteJsonData = json_decode($sXML);
            $quoteData = json_decode(json_encode($quoteJsonData), true);
            $quote=$this->quote->create(); //Create object of quote
            unset($quoteData['quote_info']['entity_id']);
            $quote->addData($quoteData['quote_info']);
            $items = array();
            foreach ($quoteData['quote_items'] as $itm) {
                unset($itm['item_id']);
                $items[] = $this->quoteItemFactory->create()->setData($itm);
            }
            
            $quote->setItems($items);
            $this->address->setData($quoteData['billing_address']);
            $this->address->setAddressType(Address::TYPE_BILLING);
            $quote->setBillingAddress($this->address);

            $this->address->setData($quoteData['shipping_address']);
            $this->address->setAddressType(Address::TYPE_SHIPPING);
            $quote->setShippingAddress($this->address);

            $this->quoteRespository->save($quote);
        }
        $this->helper->setApiData($updatedquoteAPIUrl . '/' . $oEntry->data_sync_id);
    }
}
