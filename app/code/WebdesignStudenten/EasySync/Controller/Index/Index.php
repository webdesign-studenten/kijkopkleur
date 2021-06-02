<?php
namespace WebdesignStudenten\EasySync\Controller\Index;
use WebdesignStudenten\EasySync\Model\DataIDFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Context;
class Index extends \Magento\Framework\App\Action\Action{
    protected $_dataExample;
    protected $resultRedirect;
    public function __construct (
        \Magento\Framework\App\Action\Context $context,
        \WebdesignStudenten\EasySync\Model\DataIDFactory  $dataExample,
        \Magento\Framework\Controller\ResultFactory $result
    ){
        parent::__construct($context);
        $this->_dataExample = $dataExample;
        $this->resultRedirect = $result;
    }
	public function execute(){
        $resultRedirect = $this->resultRedirect->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
//		$model = $this->_dataExample->create()->getCollection()->addFieldToFilter('dataScope', 'customer');
		$model = $this->_dataExample->create();
		$model->addData([
			"dataID" => 'Title 01',
			"ServerLocation" => 'Content 01',
			"dataScope" => 'customer',
			"UpdateDate" => '2010-10-01',
			"UpdateFlag" => 1
			]);
        $saveData = $model->save();
        if($saveData){
            $this->messageManager->addSuccess( __('Insert Record Successfully !') );
        }
		return $resultRedirect;
	}
}
 ?>