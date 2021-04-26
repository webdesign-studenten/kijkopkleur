<?php
/**
*
* Do not edit or add to this file if you wish to upgrade the module to newer
* versions in the future. If you wish to customize the module for your
* needs please contact us to https://www.milople.com/contact-us.html
*
* @category    Ecommerce
* @package     Milople_VATExempt
* @copyright   Copyright (c) 2017 Milople Technologies Pvt. Ltd. All Rights Reserved.
* @url         https://www.milople.com/magento-extensions/vat-exempt-m2.html
*
**/

namespace Milople\Vatexempt\Model;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\NoSuchEntityException;
use \Magento\Quote\Model\QuoteAddressValidator;

class VatexemptInformationManagement implements \Milople\Vatexempt\Api\VatexemptInformationManagementInterface
{
	/** @var  \Magento\Framework\View\Result\Page */
	protected $resultJsonFactory;
	protected $request;
	protected $checkoutSession;
	protected $quoteRepository;
	
	/*** @param \Magento\Framework\App\Action\Context $context  */

	public function __construct(
		\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
		\Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
		\Magento\Framework\App\RequestInterface $request,
		\Magento\Checkout\Model\Session $checkoutSession,
		\Magento\Catalog\Model\Product $productModel,
		\Magento\Catalog\Model\ProductFactory $productFactory,
		\Milople\Vatexempt\Helper\Data $data_helper,
		\Psr\Log\LoggerInterface $logger
	)
	{
		$this->logger=$logger;
		$this->resultJsonFactory = $resultJsonFactory;
		$this->quoteRepository = $quoteRepository;
		$this->request = $request;
		$this->productFactory= $productFactory;
		$this->helper = $data_helper;
		$this->_productModel = $productModel;
		$this->checkoutSession = $checkoutSession;
	}

	/**
	* @param VATExempt Json Object 
	* Set the value in session and set the tax none.
	* @return boolean
	*/
	public function saveVatexemptInformation($vatexempt)
	{		
		$result = false;
		$cartId = $this->checkoutSession->getQuoteId();
	    $quote = $this->quoteRepository->getActive($cartId);
	    $selectedStatus=$vatexempt['selectedStatus'];
		$applyTo=$this->helper->getConfig('vatexempt/generalSettings/vatexempt_apply_to');
		
		foreach ($quote->getAllVisibleItems() as $item) {
			$product=$this->productFactory->create();
			$product=$product->load($item->getProductId());
			if($selectedStatus=="1"){
				if($product->getVatstatus()=='1' || $applyTo=='1'){
					$product->setTaxClassId(0);
					$this->checkoutSession->setVatStatus(1);
					$this->checkoutSession->setVatApplientName($vatexempt['applientName']);
					$this->checkoutSession->setVatSelectedReason($vatexempt['selectedReason']);
					if(isset($vatexempt['selectedFile'])){
						$this->checkoutSession->setFile($vatexempt['selectedFile']);
					}
					if(isset($vatexempt['agreeTermsandconditions']))
					{
						$this->checkoutSession->setVatAgreeTermsandconditions($vatexempt['agreeTermsandconditions']);
					}
					else{
						$this->checkoutSession->setVatAgreeTermsandconditions('');
					}
					$item->save();
				}
			}else{
				$product->setTaxClassId(2);
				$this->checkoutSession->setVatStatus(0);
				$this->checkoutSession->setVatApplientName('');
				$this->checkoutSession->setVatSelectedReason('');
				$this->checkoutSession->setFile('');
				$this->checkoutSession->setVatAgreeTermsandconditions('');
				$item->save();
			}
	    }
		$quote->save();
		try {
			$quote->collectTotals();
			$this->quoteRepository->save($quote);
		}catch (\Exception $e) {
			throw new InputException(__($e->getMessage()));
		}
		return true;
	}
}