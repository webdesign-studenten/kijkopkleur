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

class InvoiceDataSync
{

    protected $helper;
    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $_orderRepository;

    /**
     * @var \Magento\Sales\Api\Data\InvoiceInterface
     */
    protected $invoiceInterface;

    /**
     * @var \Magento\Framework\DB\Transaction
     */
    protected $_transaction;

    public function __construct(
        \WebdesignStudenten\EasySync\Helper\Data $helper,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Sales\Api\Data\InvoiceInterface $invoiceInterface,
        \Magento\Framework\DB\Transaction $transaction,
        \Magento\Sales\Api\Data\InvoiceItemInterface $invoiceItemInterface,
        \Magento\Sales\Api\Data\InvoiceCommentInterface $commentsInterface

    ) {
        $this->helper = $helper;
        $this->_orderRepository = $orderRepository;
        $this->invoiceInterface = $invoiceInterface;
        $this->_transaction = $transaction;
        $this->invoiceItemInterface = $invoiceItemInterface;
        $this->commentsInterface = $commentsInterface;
    }

    /**
     * Execute the cron
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->helper->isEnabled()) return;
        $updatedInvoiceAPIUrl = 'rest/V1/webdesignstudenten-easysync/updatedInvoice';
        $sXML = $this->helper->getApiData($updatedInvoiceAPIUrl);
        if (empty($sXML->item)) return;

        foreach ($sXML->item as $oEntry) {
            $invoiceAPIUrl = 'rest/V1/webdesignstudenten-easysync/invoice/' . $oEntry->dataID;
            $sXML = $this->helper->getApiData($invoiceAPIUrl);
            $invoiceJsonData = json_decode($sXML);
            $invoiceData = json_decode(json_encode($invoiceJsonData), true);
            // print_r($invoiceData); die;
            
            $orderId = $invoiceData['invoice_info']['order_id']; //order id for which want to create invoice
            $order = $this->_orderRepository->get($orderId);
            if($order->canInvoice()) {
                unset($invoiceData['invoice_info']['entity_id']);
                $invoice = $this->invoiceInterface->setData($invoiceData['invoice_info']);
                // print_r($invoice->getData()); die;
                foreach ($invoiceData['invoice_items'] as $itm) {
                    unset($itm['entity_id']);
                    $items[] = $this->invoiceItemInterface->setData($itm);
                }
                foreach ($invoiceData['invoice_comments'] as $comment) {
                    unset($comment['entity_id']);
                    $comments[] = $this->commentsInterface->setData($comment);
                }
                
                $invoice->setItems($items);
                $invoice->setComments($comments);
                $invoice->register();
                $invoice->save();
                $transactionSave = $this->_transaction->addObject(
                    $invoice
                )->addObject(
                    $invoice->getOrder()
                );
                $transactionSave->save();
                // $this->invoiceSender->send($invoice);
                // //send notification code
                // $order->addStatusHistoryComment(
                //     __('Notified customer about invoice #%1.', $invoice->getId())
                // )
                // ->setIsCustomerNotified(true)
                // ->save();
            }

            

        }
        $this->helper->setApiData($updatedInvoiceAPIUrl . '/' . $oEntry->data_sync_id);



    }
}
