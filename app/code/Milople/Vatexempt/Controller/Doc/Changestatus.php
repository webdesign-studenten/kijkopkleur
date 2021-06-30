<?php

namespace Milople\Vatexempt\Controller\Doc;

class Changestatus extends \Magento\Framework\App\Action\Action
{
	protected $_pageFactory;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\Filesystem $filesystem,
		\Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
		\Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Checkout\Model\Session $checkoutSession
	) {
		$this->_request = $context->getRequest();
		$this->_pageFactory = $pageFactory;
		$this->checkoutSession = $checkoutSession;
        $this->_fileUploaderFactory = $fileUploaderFactory;
		$this->_mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
		return parent::__construct($context);
	}

	public function execute()
	{
	    $valueSet = false;
        $data = $this->getRequest()->getPostValue();
        if($data['status'] == "1"){
			$flag = 1;
			//echo "1";
			$this->checkoutSession->setVatStatus("1");
			$valueSet = true;
		}else{
			//echo "0";
			$this->checkoutSession->setVatStatus("0");
		}
    }
}
