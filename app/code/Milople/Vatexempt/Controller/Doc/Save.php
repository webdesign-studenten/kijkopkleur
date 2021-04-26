<?php

namespace Milople\Vatexempt\Controller\Doc;

class Save extends \Magento\Framework\App\Action\Action
{
	protected $_pageFactory;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\Filesystem $filesystem,
		\Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
		\Magento\Framework\View\Result\PageFactory $pageFactory
	) {
		$this->_request = $context->getRequest();
		$this->_pageFactory = $pageFactory;
		$this->_fileUploaderFactory = $fileUploaderFactory;
		$this->_mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
		return parent::__construct($context);
	}

	public function execute()
	{
		$target = $this->_mediaDirectory->getAbsolutePath('/Milople/vat/doc/');
		$allFileName = "";
		// $postdata = $this->_request->getPost();
		$files = $this->getRequest()->getFiles('files');
		//var_dump($files);
		// if(){

		// }
		if(is_array($files)){

		
		if(count($files)) {
			$i = 0;
			foreach ($files as $file) {
				
				if (isset($file['tmp_name']) && strlen($file['tmp_name']) > 0) {
					$uploader = $this->_fileUploaderFactory->create(['fileId' => $files[$i]]);
					/** Allowed extension types */
					$uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png', 'zip', 'doc']);
					$uploader->setAllowRenameFiles(true);
					/** rename file name if already exists */
					$uploader->setAllowRenameFiles(true);
					/** upload file in folder "media/Milople/rfq/data" */
					$result = $uploader->save($target);
					$allFileName .= $result['file'];
					// var_dump($result['file']);
				}
				$i++;
			}
		}
	}
	else{
		echo "else";
		var_dump('ok');
	}
		// echo "ok";
		// echo "<br>";
		//var_dump($allFileName);
		// echo "<br>";
		//var_dump($result['file']);
		// echo "next";
		// echo "<br>";
		//print_r();
		//$files->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png', 'zip', 'doc']);


		// foreach ($files as $file) {
		// 	var_dump($file);
		// }
		//echo "<br>";
		//var_dump($files);
		//echo "<br>";
		//print_r($files);
		//echo "<br>";
		//echo $result['file'];
		//exit;







	}
}
