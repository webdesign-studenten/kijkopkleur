<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Test
 * @author    Webkul
 * @copyright Copyright (c) 2010-2018 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Packs\Magento2\Model\Save;

use Magento\Framework\Model\Context;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Registry;

class Shipments extends AbstractModel
{
    protected $_orderRepository;
    protected $_convertOrder;
    protected $_shipmentNotifier;

    public function __construct(
        Context $context,
        Registry $registry,
        \Magento\Sales\Api\Data\OrderInterface $order,
        \Magento\Sales\Model\Convert\Order $convertOrder,
        \Magento\Shipping\Model\ShipmentNotifier $shipmentNotifier
    ) {
        $this->_order = $order;
        $this->_convertOrder = $convertOrder;
        $this->_shipmentNotifier = $shipmentNotifier;
        parent::__construct($context,$registry);
    }

    public function createMagentoShipments($packsOrderData)
    {
        $shipmentsData = array();

        foreach($packsOrderData as $incrementId => $jsonData) {
            $packsData = json_decode($jsonData);
            $order = $this->_order->loadByIncrementId($incrementId);

            // to check order can ship or not
            if (!$order->canShip()) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('You cant create the Shipment of this order.'));
            }

            $orderShipment = $this->_convertOrder->toShipment($order);

            foreach ($order->getAllItems() AS $orderItem) {

                // Check virtual item and item Quantity
                if (!$orderItem->getQtyToShip() || $orderItem->getIsVirtual()) {
                    continue;
                }

                $qty = $orderItem->getQtyToShip();
                $shipmentItem = $this->_convertOrder->itemToShipmentItem($orderItem)->setQty($qty);

                $orderShipment->addItem($shipmentItem);
            }

            $orderShipment->register();
            $orderShipment->getOrder()->setIsInProcess(true);
            try {

                // Save created Order Shipment
                $orderShipment->save();
                $orderShipment->getOrder()->save();

                // Send Shipment Email
                $this->_shipmentNotifier->notify($orderShipment);
                $shipment = $orderShipment->save();
                $shipment['packs'] = $packsData;
                $shipment['order_increment_id'] = $incrementId;
                array_push($shipmentsData,$shipment);
            } catch (\Exception $e) {
                $error = array($incrementId => $e->getMessage());
                return $error;
            }

        }
        return $shipmentsData;
    }
}