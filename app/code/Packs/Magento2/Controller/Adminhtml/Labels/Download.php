<?php

namespace Packs\Magento2\Controller\Adminhtml\Labels;

use Magento\Backend\App\Action;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Packs\Magento2\Model\Download\Labels as DownloadLabelsModel;
use Packs\Magento2\Model\Download\Tracktrace as DownloadTracktraceModel;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Sales\Model\Order\Shipment\TrackFactory as TrackFactory;
use Magento\Framework\App\ResourceConnection as ResourceConnection;
use Magento\Sales\Api\ShipmentRepositoryInterface as ShipmentRepository;
use Exception;
use Psr\Log\LoggerInterface;

class Download extends Action
{
    protected $_filter;
    protected $_collectionFactory;
    protected $_downloadLabelsModel;
    protected $_downloadTracktraceModel;
    protected $_messageManager;
    protected $_scopeConfig;
    protected $_shipmentRepository;
    protected $_resourceConnection;
    protected $_trackFactory;
    private $logger;

    public function __construct(
        Action\Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        FileFactory $fileFactory,
        DownloadLabelsModel $downloadLabelsModel,
        DownloadTracktraceModel $downloadTracktraceModel,
        ManagerInterface $messageManager,
        TrackFactory $trackFactory,
        ShipmentRepository $shipmentRepository,
        ResourceConnection $resourceConnection,
        LoggerInterface $logger
    ) {
        $this->_filter = $filter;
        $this->_collectionFactory = $collectionFactory;
        $this->_downloadLabelsModel = $downloadLabelsModel;
        $this->_downloadTracktraceModel = $downloadTracktraceModel;
        $this->_downloader =  $fileFactory;
        $this->_trackFactory = $trackFactory;
        $this->_shipmentRepository = $shipmentRepository;
        $this->_messageManager = $messageManager;
        $this->_resourceConnection = $resourceConnection;
        $this->logger = $logger;
        parent::__construct($context);
    }

    public function execute()
    {
        $collection = $this->_filter->getCollection($this->_collectionFactory->create());

        $entityDeleted = 0;
        $orderIds = array();
        foreach ($collection->getAllIds() as $id) {
            array_push($orderIds, $id);
        }

        $response = $this->_downloadLabelsModel->downloadLabels($orderIds);
        $tracktraceresponse = $this->_downloadTracktraceModel->downloadTracktrace($orderIds);
        if(isset($tracktraceresponse) && is_array($tracktraceresponse)){
            foreach($tracktraceresponse as $tracktraceitem){
                $connection = $this->_resourceConnection->getConnection();
                $table = $this->_resourceConnection->getTableName('packs_magento2_shipment');
                $query = "SELECT * FROM ".$table." WHERE packs_shipment_id = '" . $tracktraceitem['batch'] . "'";
                $result = $connection->fetchAll($query);
                if($result){
                    $shipmentId = $result[0]['magento_shipment_id'];
                    $shipment = $this->getShipmentById($shipmentId);
                    $trackNumbers = NULL;
                    if(isset($shipment)){
                        if(null !== $shipment->getTracksCollection()){
                            $tracksCollection = $shipment->getTracksCollection();
                            foreach ($tracksCollection as $track) {
                                $trackNumbers[] = $track->getTrackNumber();
                            }
                        }

                        if ($shipment->getId() != '' && empty($trackNumbers)) {
                            $this->addTrack($shipment, 'custom', $tracktraceitem['batch'], $tracktraceitem['trackAndTraceUrl']);
                        }
                    }
                }
            }
        }
        if(is_array($response)){
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('sales/order/index');
            $this->_messageManager->addError($response['error']);
            return $resultRedirect;
        }elseif ($response == NULL){
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('sales/order/index');
            $this->_messageManager->addError('API did not return a label');
            return $resultRedirect;
        }
        else{
            $filename = $response;
            return $this->_downloader->create(
                $filename,
                @file_get_contents($filename)
            );
        }
    }

    public function getShipmentById($id)
    {
        try {
            $shipment = $this->_shipmentRepository->get($id);
        } catch (Exception $exception)  {
            $this->logger->critical($exception->getMessage());
            $shipment = null;
        }
        return $shipment;
    }

    public function addTrack($shipment, $carrierCode, $description, $trackingNumber)
    {
        /** Creating Tracking */
        /** @var Track $track */
        $track = $this->_trackFactory->create();
        $track->setCarrierCode($carrierCode);
        $track->setDescription($description);
        $track->setTrackNumber($trackingNumber);
        $shipment->addTrack($track);
        $this->_shipmentRepository->save($shipment);

        /* Notify the customer*/
        //$this->_shipmentNotifier->notify($shipment);
    }
}