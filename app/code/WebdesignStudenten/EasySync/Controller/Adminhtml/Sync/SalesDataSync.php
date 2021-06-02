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

namespace WebdesignStudenten\EasySync\Controller\Adminhtml\Sync;

class SalesDataSync extends \Magento\Backend\App\Action
{

    protected $helper;

    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \WebdesignStudenten\EasySync\Helper\Data $helper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Quote\Model\QuoteManagement $quoteManagement,
        \WebdesignStudenten\EasySync\Cron\QuoteDataSync $quoteDataSync,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepositoryInterface

    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->helper = $helper;
        $this->_storeManager = $storeManager;
        $this->quoteManagement = $quoteManagement;
        $this->quoteDataSync = $quoteDataSync;
        $this->cartRepositoryInterface = $cartRepositoryInterface;
    }

    /**
     * Execute the cron
     *
     * @return void
     */
    public function execute()
    {
        $this->quoteDataSync->execute();
        $updatedOrderAPIUrl = 'rest/V1/webdesignstudenten-easysync/updatedSales';
        $sXML = $this->helper->getApiData($updatedOrderAPIUrl);
        if (empty($sXML->item)) return;

        foreach ($sXML->item as $oEntry) {
            $orderAPIUrl = 'rest/V1/webdesignstudenten-easysync/sales/' . $oEntry->dataID;
            $sXML = $this->helper->getApiData($orderAPIUrl);
            if ($sXML == false) {
                $this->helper->setApiData($updatedOrderAPIUrl . '/' . $oEntry->data_sync_id);
                continue;
            }
            $orderJsonData = json_decode($sXML);
            $orderData = json_decode(json_encode($orderJsonData), true);
            // print_r($orderData); die;

            $store = $this->_storeManager->getStore($orderData['order_info']['store_id']);
            $websiteId = $store->getWebsiteId();
            
            $quote=$this->cartRepositoryInterface->get($orderData['order_info']['quote_id']); //Create object of quote
            
            $quote->setStore($store); //set store for which you create quote

            //Set Address to quote
            $quote->getBillingAddress()->addData($orderData['billing_address']);
            $quote->getShippingAddress()->addData($orderData['shipping_address']);

            // Collect Rates and Set Shipping & Payment Method

            $shippingAddress=$quote->getShippingAddress();
            $shippingAddress->setCollectShippingRates(true)
                            ->collectShippingRates()
                            ->setShippingMethod($orderData['order_info']['shipping_method']); //shipping method

            $quote->setPaymentMethod($orderData['payment_info']['method']); //payment method
            // $quote->setInventoryProcessed(false); //not effetc inventory
            $quote->save(); //Now Save quote and your quote is ready

            // Set Sales Order Payment
            $quote->getPayment()->importData(['method' => $orderData['payment_info']['method']]);

            // Collect Totals & Save Quote
            $quote->collectTotals()->save();
            $quote->setCustomerEmail($orderData['order_info']['customer_email']);
            // Create Order From Quote
            $order = $this->quoteManagement->submit($quote);
            // $order->setEmailSent(0);
            $order->setEmailSent($orderData['order_info']['send_email']);
            
        }
        $this->helper->setApiData($updatedOrderAPIUrl . '/' . $oEntry->data_sync_id);

    }
}
