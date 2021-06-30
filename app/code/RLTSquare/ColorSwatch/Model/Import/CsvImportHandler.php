<?php
/**
 *
 * @category : RLTSquare
 * @Package  : RLTSquare_ColorSwatch
 * @Author   : RLTSquare <support@rltsquare.com>
 * @copyright Copyright 2021 © rltsquare.com All right reserved
 * @license https://rltsquare.com/
 */
namespace RLTSquare\ColorSwatch\Model\Import;
use Magento\Framework\DB\Transaction;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Sales\Model\Service\InvoiceService;

/**
 * Class CsvImportHandler
 * @package RLTSquare\ColorSwatch\Model\Import
 */
class CsvImportHandler
{
    /**
     * @var
     */
    protected $_publicStores;
    /**
     * @var \Magento\Tax\Model\Calculation\RateFactory
     */
    protected $_taxRateFactory;
    /**
     * @var
     */
    protected $PromotedProducts;
    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $_productRepository;
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;
    /**
     * @var \Magento\Quote\Model\Quote\PaymentFactory
     */
    protected $paymentfactory;
    /**
     * @var \Magento\Framework\File\Csv
     */
    protected $csvProcessor;
    /**
     * @var \Magento\Sales\Model\Order\Status\HistoryFactory
     */
    public $orderHistoryFactory;
    /**
     * @var InvoiceService
     */
    public $invoiceService; // For Invoicing
    /**
     * @var Transaction
     */
    public $transaction;//For Invoice
    /**
     * @var InvoiceSender
     */
    public $invoiceSender;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $_storeManager;
    /**
     * @var \Magento\Sales\Model\Convert\Order
     */
    public $_convertOrder; //For Shipment
    /**
     * @var \Magento\Shipping\Model\ShipmentNotifier
     */
    public $_shipmentNotifier;
    /**
     * @var \RLTSquare\ColorSwatch\Helper\Data
     */
    public $helper;
    /**
     * @var \RLTSquare\ColorSwatch\Model\ResourceModel\ColorSwatch
     */
    public $modelolorSwatch;

    /**
     * CsvImportHandler constructor.
     * @param \Magento\Store\Model\ResourceModel\Store\Collection $storeCollection
     * @param \Magento\Tax\Model\Calculation\RateFactory $taxRateFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Framework\File\Csv $csvProcessor
     * @param \RLTSquare\ColorSwatch\Model\ResourceModel\ColorSwatch $modelolorSwatch
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Framework\Data\Form\FormKey $formkey
     * @param \Magento\Quote\Model\QuoteFactory $quote
     * @param \Magento\Quote\Model\Quote\PaymentFactory $paymentfactory
     * @param \Magento\Quote\Model\QuoteManagement $quoteManagement
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Sales\Model\Service\OrderService $orderService
     * @param \Magento\Sales\Model\Order\Status\HistoryFactory $historyFactory
     * @param InvoiceService $invoiceService
     * @param InvoiceSender $invoiceSender
     * @param Transaction $transaction
     * @param \Magento\Sales\Model\Convert\Order $convertOrder
     * @param \RLTSquare\ColorSwatch\Helper\Data $helper
     * @param \Magento\Shipping\Model\ShipmentNotifier $shipmentNotifier
     */
    public function __construct(
        \Magento\Store\Model\ResourceModel\Store\Collection $storeCollection,
        \Magento\Tax\Model\Calculation\RateFactory $taxRateFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\File\Csv $csvProcessor,
        \RLTSquare\ColorSwatch\Model\ResourceModel\ColorSwatch $modelolorSwatch,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Product $product,
        \Magento\Framework\Data\Form\FormKey $formkey,
        \Magento\Quote\Model\QuoteFactory $quote,
        \Magento\Quote\Model\Quote\PaymentFactory $paymentfactory,
        \Magento\Quote\Model\QuoteManagement $quoteManagement,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Sales\Model\Service\OrderService $orderService,
        \Magento\Sales\Model\Order\Status\HistoryFactory $historyFactory,
        InvoiceService $invoiceService,
        InvoiceSender $invoiceSender,
        Transaction $transaction,
        \Magento\Sales\Model\Convert\Order $convertOrder,
        \RLTSquare\ColorSwatch\Helper\Data $helper,
        \Magento\Shipping\Model\ShipmentNotifier $shipmentNotifier
    ) {
        // prevent admin store from loading
        $this->orderHistoryFactory = $historyFactory; //For order comments
        $this->invoiceService = $invoiceService; // For Invoicing
        $this->transaction = $transaction;//For Invoice
        $this->invoiceSender = $invoiceSender;
        $this->_storeManager = $storeManager;
        $this->_convertOrder = $convertOrder; //For Shipment
        $this->_shipmentNotifier = $shipmentNotifier; //For Shipment
        $this->_product = $product;
        $this->_formkey = $formkey;
        $this->modelolorSwatch = $modelolorSwatch;
        $this->helper = $helper;
        $this->quote = $quote;
        $this->paymentfactory = $paymentfactory;
        $this->quoteManagement = $quoteManagement;
        $this->_productRepository = $productRepository;
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
        $this->orderService = $orderService;
        $this->_productFactory = $productFactory;
        $this->_publicStores = $storeCollection->setLoadDefault(false);
        $this->_taxRateFactory = $taxRateFactory;
        $this->csvProcessor = $csvProcessor;
    }

    /**
     * @return array
     */
    public function getRequiredCsvFields()
    {
        // indexes are specified for clarity, they are used during import
        return [
            0 => __('Column1'),
            1 => __('sku'),
            2 => __('Niveau Colours.Column2'),
            3 => __('Niveau Colours.Column3'),
            4 => __('Niveau Colours.Column4')
        ];
    }

    /**
     * @param $file
     */
    public function importFromCsvFile($file)
    {

        // Always first increase the size of a php.ini file when u upload a bigger file
        if (!isset($file['tmp_name'])) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Invalid file upload attempt.'));
        }
        $faqsRawData = $this->csvProcessor->getData($file['tmp_name']);
        // first row of file represents headers
        $fileFields = $faqsRawData[0];
       
        


        $validFields = $this->_filterFileFields($fileFields);
        if (!$validFields) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Invalid file fields found.'));
        }
        foreach ($faqsRawData as $rowIndex => $dataRow) {
            // skip headers
            if ($rowIndex == 0) {
                continue;
            }
            $faqsCache = $this->_importFaq($dataRow);
        }
    }
    /**
     * Filter file fields (i.e. unset invalid fields)
     *
     * @param array $fileFields
     * @return string[] filtered fields
     */
    protected function _filterFileFields(array $fileFields)
    {
        $requiredFields = $this->getRequiredCsvFields();
        $requiredFieldsNum = count($this->getRequiredCsvFields());
        $fileFieldsNum = count($fileFields);
        if ($fileFieldsNum < $requiredFieldsNum) {
            return false;
        }
        // check the required fields availability in file
        for ($index = 0; $index < $requiredFieldsNum; $index++) {
            if (array_search(strtolower($requiredFields[$index]), array_map('strtolower', $fileFields)) === false) {
                return false;
            }
        }
        return true;
    }
    /**
     * Import single rate
     *
     * @param array $faqData
     * @param array $regionsCache cache of regions of already used countries (is used to optimize performance)
     * @param array $storesCache cache of stores related to tax rate titles
     * @return array regions cache populated with regions related to country of imported tax rate
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _importFaq(array $faqData)
    {
        $storeArray = [];
        $storeArray['attribute_value'] = $faqData['0'];
        $storeArray['product_sku'] = $faqData['1'];
        $storeArray['color_rgb'] = $faqData['2'];
        $storeArray['color_hash'] = $faqData['3'];
        $storeArray['color_name'] = $faqData['4'];
        if($storeArray['product_sku']) {
            $optionid = $this->helper->createOrGetId('company_shades',$storeArray['attribute_value']);
            // create product using sku
            // $prodid = $this->createProduct($storeArray['product_sku'],$storeArray['color_hash']);
            $colorswatchid = $this->modelolorSwatch->CreateorCheck($optionid);
            $prodid = $this->getProductIdandSetColor($storeArray['product_sku'],$storeArray['color_hash']);
            if ( $colorswatchid && $prodid ) {
                $this->modelolorSwatch->AttachedProductid($prodid,$colorswatchid);
            } else {
                $colorswatchid = $this->modelolorSwatch->getColorSwatchId($optionid);
                $colorswatchid = $colorswatchid['0']['colorswatch_id'];
                $this->modelolorSwatch->AttachedProductid($prodid,$colorswatchid);
            }
        }
    }
    /**
     * @param $orderData
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProductIdandSetColor($sku,$color)
    {
        $product = $this->_product->loadByAttribute('sku', $sku);
        // if($product->getId()) {
            $product->setStoreId(0);
            $product->setBackgroundColor($color);
            $product->save();
            return $product->getId();
        //     }
        // return 0;
       
    }
    // public function createProduct($sku,$color)
    // {
    //     $product = $this->_product;
    //     $product->setSku($sku); // Set your sku here
    //     $product->setName($sku); // Name of Product
    //     $product->setAttributeSetId(4); // Attribute set id
    //     $product->setStatus(1); // Status on product enabled/ disabled 1/0
    //     $product->setWeight(10); // weight of product
    //     $product->setVisibility(4); // visibilty of product (catalog / search / catalog, search / Not visible individually)
    //     $product->setTaxClassId(0); // Tax class id
    //     $product->setTypeId('simple'); // type of product (simple/virtual/downloadable/configurable)
    //     $product->setPrice(100); // price of product
    //     $product->setStockData(
    //         array(
    //             'use_config_manage_stock' => 0,
    //             'manage_stock' => 1,
    //             'is_in_stock' => 1,
    //             'qty' => 999999999
    //         )
    //     );
    //     $product->setBackgroundColor($color);
    //     $product->save();
    //     return $product->getId();
    // }
}
