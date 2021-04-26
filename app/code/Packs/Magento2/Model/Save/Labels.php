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
use Magento\Framework\Message\ManagerInterface;
use Packs\Magento2\Model\Labels as PacksLabelsModel;
use Magento\Framework\App\ResourceConnection;

class Labels extends AbstractModel
{
    protected $_packsLabelsModel;
    protected $_messageManager;
    protected $_resourceConnection;

    public function __construct(
        Context $context,
        Registry $registry,
        PacksLabelsModel $packsLabelsModel,
        ManagerInterface $messageManager,
        ResourceConnection $resourceConnection
    ) {
        $this->_packsLabelsModel = $packsLabelsModel;
        $this->_messageManager = $messageManager;
        $this->_resourceConnection = $resourceConnection;
        parent::__construct($context,$registry);
    }
    public function savePacksLabels($shipmentsData){
        foreach($shipmentsData as $shipment) {
            $packsData = (array)$shipment['packs'];
            $orderIncrementId = $shipment['order_increment_id'];
            $savedSuccesfully = false;
            foreach ($packsData['shipmentItems'] as $packsShipmentLabel) {
                try {
                    $packsShipmentLabel = (array)$packsShipmentLabel;
                    if(!isset($packsShipmentLabel['labelObject'])){
                        $packsShipmentLabel['labelObject'] = '';
                    }
                    $labelData = array(
                        'magento_order_id' => $shipment['order_id'],
                        'magento_shipment_id' => $shipment['order_id'],
                        'packs_shipment_id' => $packsData['shipmentId'],
                        'packs_shipment_item_id' => $packsShipmentLabel['shipmentItemId'],
                        'label' => $packsShipmentLabel['labelObject'],
                        'label_type' => 'PDF',
                    );

                    $this->_packsLabelsModel->setData($labelData);
                    $this->_packsLabelsModel->save();
                    $savedSuccesfully = true;
                } catch (Exception $e) {
                    $savedSuccesfully = false;
                    continue;
                }
            }
        }
        if($savedSuccesfully){
            $this->_messageManager->addSuccess($orderIncrementId.' : Exported succesfully');
        }else{
            $this->_messageManager->addError($orderIncrementId.' : '.$e->getMessage());
        }
        return;
    }

    public function savePacksLabelsOnly($labelData){
        foreach($labelData['labels'] as $label){
            $connection = $this->_resourceConnection->getConnection();
            $query = "
                UPDATE `packs_magento2_labels` 
                SET label='" . $label['labelObject'] . "' 
                WHERE packs_shipment_item_id = '" . $label['shipmentItemId'] . "'
            ";
            $connection->query($query);
        }
    }
}