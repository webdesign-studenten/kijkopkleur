<?php
namespace WebdesignStudenten\EasySync\Controller\Index;
class Customer extends \Magento\Framework\App\Action\Action{
    protected $customerRepository;
    protected $resultRedirect;
        /**
     * Customer registry.
     *
     * @var \Magento\Customer\Model\CustomerRegistry
     */
    protected $_customerRegistry;
    public function __construct (
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\ResourceModel\CustomerRepository  $customerRepository,
        \Magento\Customer\Model\CustomerRegistry $customerRegistry
    ){
        parent::__construct($context);
        $this->customerRepository = $customerRepository;
        $this->_customerRegistry   = $customerRegistry;
    }
	public function execute(){
//        $customerSecure = $this->_customerRegistry->retrieveSecureData('19');
        echo '<pre>';
//        print_r($customerSecure->getData());
//        echo $customerSecure->getRpToken() . "<br />";
//        echo $customerSecure->getRpTokenCreatedAt() . "<br />";
//        print_r($customerSecure->getPasswordHash());
        $customer = $this->_customerRegistry->retrieve('19');
        print_r($customer->getData()); die;
        // setting customer password
//        $this->customerRepository->save($customer, $customerSecure->getPasswordHash());
        
	}
}
 ?>