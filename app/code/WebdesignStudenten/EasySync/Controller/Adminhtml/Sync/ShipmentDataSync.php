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

class ShipmentDataSync extends \Magento\Backend\App\Action
{
    protected $helper;
    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $_orderRepository;

    /**
     * @var \Magento\Sales\Api\Data\shipmentInterface
     */
    protected $shipmentInterface;

    /**
     * @var \Magento\Framework\DB\Transaction
     */
    protected $_transaction;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \WebdesignStudenten\EasySync\Helper\Data $helper,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Sales\Api\Data\ShipmentInterface $shipmentInterface,
        \Magento\Framework\DB\Transaction $transaction,
        \Magento\Sales\Api\Data\ShipmentItemInterface $shipmentItemInterface,
        \Magento\Sales\Api\Data\ShipmentCommentInterface $commentsInterface

    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->helper = $helper;
        $this->_orderRepository = $orderRepository;
        $this->shipmentInterface = $shipmentInterface;
        $this->_transaction = $transaction;
        $this->shipmentItemInterface = $shipmentItemInterface;
        $this->commentsInterface = $commentsInterface;
    }

    /**
     * Execute the cron
     *
     * @return void
     */
    public function execute()
    {
        $updatedShipmentAPIUrl = 'rest/V1/webdesignstudenten-easysync/updatedShipment';
        $sXML = $this->helper->getApiData($updatedShipmentAPIUrl);
        if (empty($sXML->item)) return;

        foreach ($sXML->item as $oEntry) {
            $shipmentAPIUrl = 'rest/V1/webdesignstudenten-easysync/shipment/' . $oEntry->dataID;
            $sXML = $this->helper->getApiData($shipmentAPIUrl);
            if ($sXML == false) {
                $this->helper->setApiData($updatedShipmentAPIUrl . '/' . $oEntry->data_sync_id);
                continue;
            }
            $shipmentJsonData = json_decode($sXML);
            $shipmentData = json_decode(json_encode($shipmentJsonData), true);
            // print_r($shipmentData); die;
            
            $orderId = $shipmentData['shipment_info']['order_id']; //order id for which want to create shipment
            $order = $this->_orderRepository->get($orderId);
            if($order->canShip()) {
                unset($shipmentData['shipment_info']['entity_id']);
                $shipment = $this->shipmentInterface->setData($shipmentData['shipment_info']);
                // print_r($shipment->getData()); die;
                foreach ($shipmentData['shipment_items'] as $itm) {
                    unset($itm['entity_id']);
                    $items[] = $this->shipmentItemInterface->setData($itm);
                }
                foreach ($shipmentData['shipment_comments'] as $comment) {
                    unset($comment['entity_id']);
                    $comments[] = $this->commentsInterface->setData($comment);
                }
                
                $shipment->setItems($items);
                $shipment->setComments($comments);
                $shipment->register();
                $shipment->save();
                $transactionSave = $this->_transaction->addObject(
                    $shipment
                )->addObject(
                    $shipment->getOrder()
                );
                $transactionSave->save();
            }

        }
        $this->helper->setApiData($updatedShipmentAPIUrl . '/' . $oEntry->data_sync_id);
    }
}
