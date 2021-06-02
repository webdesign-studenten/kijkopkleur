<?php

namespace WebdesignStudenten\EasySync\Controller\Adminhtml\Sync;

class CustomerDataSync extends \Magento\Backend\App\Action
{

    protected $logger;
    protected $helper;
    /**
     * @var CustomerFactory
     */
    private $customerRegistry;
    private $customerFact;
    private $customerFactory;
    private $customer;
    /**
     * @var Magento\Customer\Model\AddressFactory
     */
    protected $addressDataFactory;


    /**
     * @var \Magento\Framework\View\Result\PageFactory
    */
    protected $resultPageFactory;

    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Psr\Log\LoggerInterface $logger,
        \WebdesignStudenten\EasySync\Helper\Data $helper,
        \WebdesignStudenten\EasySync\Api\Data\CustomerInterface $customer,
        \Magento\Customer\Model\CustomerRegistry $customerRegistry,
        \Magento\Customer\Model\Customer $customerFact,
        \Magento\Customer\Model\ResourceModel\CustomerRepository $customerFactory,
        \Magento\Customer\Model\AddressFactory $addressDataFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->logger = $logger;
        $this->helper = $helper;
        $this->customer = $customer;
        $this->customerRegistry = $customerRegistry;
        $this->customerFact = $customerFact;
        $this->customerFactory = $customerFactory;
        $this->addressDataFactory = $addressDataFactory;
    }
    
    /**
     * @param string     $email
     * @param null $websiteId
     *
     * @return bool|\Magento\Customer\Model\Customer
     */
    public function customerExists($email, $websiteId = null)
    {
        $customer = $this->customerFact;
        if ($websiteId) {
            $customer->setWebsiteId($websiteId);
        }
        $customer->loadByEmail($email);
        if ($customer->getId()) {
            return $customer;
        }

        return false;
    }

    /**
     * Execute the cron
     *
     * @return void
     */
    public function execute()
    {
        
        $updatedCustomerAPIUrl = 'rest/V1/webdesignstudenten-easysync/updatedcustomer';
        $sXML = $this->helper->getApiData($updatedCustomerAPIUrl);
        foreach($sXML->item as $oEntry){
            try{
                $customerAPIUrl = 'rest/V1/webdesignstudenten-easysync/customer/' . $oEntry->dataID;
                $sXML = $this->helper->getApiData($customerAPIUrl);
                if ($sXML == false) {
                    $this->helper->setApiData($updatedCustomerAPIUrl . '/' . $oEntry->data_sync_id);
                    continue;
                }
                $customerData = json_decode($sXML);
                $customerDataAddress = json_decode(json_encode($customerData), true);
                $customerDataArray = $customerDataAddress['customerData'];
                $customerAddressArray = $customerDataAddress['customerAddress'];
                $custId = '';
                if ($customerModel = $this->customerExists($customerDataArray['email'], $customerDataArray['website_id'])) {
                    unset($customerDataArray['entity_id']);
                    foreach ($customerDataArray as $key => $value) {
                        $customerModel->setData($key, $value);
                    }
    //                $this->customerFactory->save($customerModel, $customerDataArray['password_hash']);
                    $customerModel->save();
                    $custId = $customerModel->getId();
                } else {
                    foreach ($customerDataArray as $key => $value) {
                        $this->customer->setData($key, $value);
                    }
                    $this->customerFactory->save($this->customer, $customerDataArray['password_hash']);
                    $custId = $this->customer->getId();
                }
                // Saving customer addresses
                
                foreach ($customerAddressArray as $customerAddress) {
                    $address = $this->addressDataFactory->create();
                    foreach ($customerAddress as $key => $value) {
                        if ($key == 'attributes') continue;
                        if ($key == 'customer_id') $value = $custId;
                        if ($key == 'parent_id') $value = $custId;
                        $address->setData($key, $value);
                    }
    //                print_r($address->getData()); die;
                    $address->save();
                }
            } catch (\Exception $e) {
                $this->helper->setApiData($updatedCustomerAPIUrl . '/' . $oEntry->data_sync_id);
                $this->_logger->critical('Error Curl', ['exception' => $e]);
                continue;
            }
            $this->helper->setApiData($updatedCustomerAPIUrl . '/' . $oEntry->data_sync_id);
        }
    }
}
