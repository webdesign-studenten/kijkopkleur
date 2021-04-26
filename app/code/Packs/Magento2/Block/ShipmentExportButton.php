<?php

namespace Packs\Magento2\Block;

use Magento\Framework\View\Element\Template;
use Packs\Magento2\Model\ResourceModel\Shipment\Collection;
use Packs\Magento2\Model\ResourceModel\Shipment\CollectionFactory;

class ShipmentExportButton extends Template
{
    private $collectionFactory;

    public function __construct(
        Template\Context $context,
        CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return \Packs\Magento2\Model\Shipment[]
     */
    public function getShipment()
    {
        return $this->collectionFactory->create()->getItem();
    }
}
