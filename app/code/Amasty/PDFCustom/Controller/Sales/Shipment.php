<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_PDFCustom
 */


namespace Amasty\PDFCustom\Controller\Sales;

class Shipment extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Sales\Controller\AbstractController\OrderViewAuthorizationInterface
     */
    private $orderAuthorization;

    /**
     * @var \Magento\Sales\Controller\AbstractController\OrderLoaderInterface
     */
    private $orderLoader;

    /**
     * @var \Magento\Sales\Api\ShipmentRepositoryInterface
     */
    private $shipmentRepository;

    /**
     * @var \Amasty\PDFCustom\Model\Order\Pdf\Shipment
     */
    private $shipmentPdf;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    private $fileFactory;

    public function __construct(
        \Magento\Sales\Controller\AbstractController\OrderViewAuthorizationInterface $orderAuthorization,
        \Magento\Sales\Controller\AbstractController\OrderLoaderInterface $orderLoader,
        \Magento\Sales\Api\ShipmentRepositoryInterface $shipmentRepository,
        \Amasty\PDFCustom\Model\Order\Pdf\Shipment $shipmentPdf,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\App\Action\Context $context
    ) {
        $this->orderAuthorization = $orderAuthorization;
        $this->orderLoader = $orderLoader;
        $this->shipmentRepository = $shipmentRepository;
        $this->shipmentPdf = $shipmentPdf;
        $this->registry = $registry;
        $this->fileFactory = $fileFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $shipmentId = (int)$this->getRequest()->getParam('shipment_id');
        if ($shipmentId) {
            /** @var \Magento\Sales\Model\Order\Shipment $shipment */
            $shipment = $this->shipmentRepository->get($shipmentId);
            $order = $shipment->getOrder();
            $this->registry->register('current_order', $order);
        } else {
            $result = $this->orderLoader->load($this->_request);
            if ($result instanceof \Magento\Framework\Controller\ResultInterface) {
                return $result;
            }
            /** @var \Magento\Sales\Model\Order $order */
            $order = $this->registry->registry('current_order');
        }

        try {
            if (!$this->orderAuthorization->canView($order)) {
                return $this->getRedirect();
            }

            /** @var \Amasty\PDFCustom\Model\Pdf $pdf */
            if (isset($shipment)) {
                $filename = 'shipment' . $shipment->getIncrementId() . '.pdf';
                $pdf = $this->shipmentPdf->getPdf([$shipment]);
            } else {
                $filename = 'shipments_of_order' . $order->getIncrementId() . '.pdf';
                $pdf = $this->shipmentPdf->getPdf($order->getShipmentsCollection());
            }

            $content = $pdf->render();

            $response = $this->fileFactory->create(
                $filename,
                $content,
                \Magento\Framework\App\Filesystem\DirectoryList::TMP,
                'application/pdf',
                strlen($content)
            );
            // avoid double headers or double content
            return $response;
        } catch (\Exception $e) {
            return $this->getRedirect();
        }
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface
     */
    protected function getRedirect()
    {
        return $this->_redirect('sales/order/history');
    }
}
