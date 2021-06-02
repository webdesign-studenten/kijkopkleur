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

class SalesDataSync
{

    protected $helper;

    /**
    * @param Magento\Framework\App\Helper\Context $context
    * @param Magento\Store\Model\StoreManagerInterface $storeManager
    * @param Magento\Catalog\Model\Product $product
    * @param Magento\Framework\Data\Form\FormKey $formKey $formkey,
    * @param Magento\Quote\Model\Quote $quote,
    * @param Magento\Customer\Model\CustomerFactory $customerFactory,
    * @param Magento\Sales\Model\Service\OrderService $orderService,
    */
    public function __construct(
        \WebdesignStudenten\EasySync\Helper\Data $helper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Product $product,
        \Magento\Framework\Data\Form\FormKey $formkey,
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Model\QuoteManagement $quoteManagement,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Sales\Model\Service\OrderService $orderService,
        \Magento\Directory\Model\CurrencyFactory $currency,
        \WebdesignStudenten\EasySync\Cron\QuoteDataSync $quoteDataSync,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepositoryInterface

    ) {
        $this->helper = $helper;
        $this->_storeManager = $storeManager;
        $this->_product = $product;
        $this->_formkey = $formkey;
        $this->quote = $quote;
        $this->quoteManagement = $quoteManagement;
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
        $this->orderService = $orderService;
        $this->currency = $currency;
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
        if (!$this->helper->isEnabled()) return;
        $this->quoteDataSync->execute();
        $updatedOrderAPIUrl = 'rest/V1/webdesignstudenten-easysync/updatedSales';
        $sXML = $this->helper->getApiData($updatedOrderAPIUrl);
        if (empty($sXML->item)) return;

        foreach ($sXML->item as $oEntry) {
            $orderAPIUrl = 'rest/V1/webdesignstudenten-easysync/sales/' . $oEntry->dataID;
            $sXML = $this->helper->getApiData($orderAPIUrl);
            $orderJsonData = json_decode($sXML);
            $orderData = json_decode(json_encode($orderJsonData), true);
            // print_r($orderData); die;

            $store = $this->_storeManager->getStore($orderData['order_info']['store_id']);
            $websiteId = $store->getWebsiteId();
            // $customer=$this->customerFactory->create();
            // $customer->setWebsiteId($websiteId);
            // $customer->loadByEmail($orderData['order_info']['customer_email']);
            $quote=$this->cartRepositoryInterface->get($orderData['order_info']['quote_id']); //Create object of quote
//            echo $orderData['order_info']['quote_id'];
  //      print_r($quote->getData()); die;
            $quote->setStore($store); //set store for which you create quote
            // // if you have allready buyer id then you can load customer directly
            //  //Assign quote to customer
            // if(!$customer->getEntityId()){
            //     //If not avilable then create this customer
            //     $customer->setWebsiteId($websiteId)
            //             ->setStore($store)
            //             ->setFirstname($orderData['shipping_address']['firstname'])
            //             ->setLastname($orderData['shipping_address']['lastname'])
            //             ->setEmail($orderData['order_info']['customer_email'])
            //             ->setCustomerIsGuest(1);

            //     $customer->save();
            // }
            // $customer= $this->customerRepository->getById($customer->getEntityId());
            // $quote->assignCustomer($customer);
            // // $orderedCurrency = $this->currency->create()->load($orderData['order_info']['order_currency_code']);
            // $quote->setCurrency();

            //add items in quote
            // foreach($orderData['order_items'] as $item){
            //     $product=$this->_product->load($item['product_id']);
            //     $product->setPrice($item['price']);
            //     $quote->addProduct(
            //         $product,
            //         intval($item['product_options']['info_buyRequest']['qty'])
            //     );
            // }

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
            // $increment_id = $order->getRealOrderId();
            // if($order->getEntityId()){
            //     $result['order_id']= $order->getRealOrderId();
            // }else{
            //     $result=['error'=>1,'msg'=>'Your custom message'];
            // }
            // return $result;

        }
        $this->helper->setApiData($updatedOrderAPIUrl . '/' . $oEntry->data_sync_id);



    }
}
