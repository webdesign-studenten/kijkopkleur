<?php 
namespace Milople\Vatexempt\Controller\Adminhtml\Conditions;

use Magento\Backend\App\Action\Context;
 use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Exception; 

class Download extends \Magento\Framework\App\Action\Action

{
	//    protected $_publicActions = ['Download'];
	 
	   protected $resultPageFactory;
	  
	   public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Framework\Filesystem\Io\File $filesystem,
        \Milople\Vatexempt\Model\DetailsFactory $detailsFactory,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\App\Filesystem\DirectoryList $directory_list
    ) {
		
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->filesystem=$filesystem;
        $this->request = $request;
       $this->Details=$detailsFactory->create();
        $this->directory_list= $directory_list;
    } 
	
	
	public function execute()
    {
        // echo $this->getRequest()->getParam('detail_id');
        // var_dump($d);
        $details=$this->Details->load($this->getRequest()->getParam('detail_id'))->getData();
        $mulfiles = $details['file'];
        //$this->create_zip($files, "milople_Vatexempt.zip");
        $files = explode(',',$mulfiles);
        $customDir = $this->directory_list->getPath('media').'/Milople/vat/doc/';
	   foreach ($files as $file) {
		$file_arr[]= $customDir.$file;
		}
       
        if (count($file_arr)!=0)
		{
            $result = $this->create_zip($file_arr, "milople_Vatexempt.zip");
        }


        // $files = $details['file']  ;
        // $this->create_zip($files, "milople_Vatexempt.zip");
        
        
        // print_r($files);
        // echo $files;
        // exit;
        
        // return;
        // echo "ok"; exit;
      // echo "rfff";exit();
    //    $customDir = $this->directory_list->getPath('media').'/Milople/vat/doc/';
	//    //print_r($customDir);
	//     $file_arr=array();
	//    //$files =$this->getRequest()->getParam('theFile');
	//   // print_r($files); 
	//    $conditions = $this->conditions->load($this->getRequest()->getParam('detail_id'));
	//   // print_r($conditions); 
	//    $files = explode(',',$conditions->getFile());
	//    foreach ($files as $file) {
        // $file_arr[]= $customDir.$files;
        // $result = $this->create_zip($file_arr, "milople_Vatexempt.zip");
	// 	}
       
    //     if (count($file_arr)!=0)
	// 	{
    //         
    //     }
       
	   
	   
		
		
	}
	
	// protected function _isAllowed()
    // {
    //     return true;
    // }
    
    function create_zip($files = array(), $destination)
    {
       
        //vars
        $valid_files = array();
         $overwrite = false;
        //if files were passed in...
        if(is_array($files)) {
            
            //cycle through each file
            foreach($files as $file) {
                //make sure the file exists
              
                if(file_exists($file)) {
                    $valid_files[] = $file;
                }
            }
        }
        //if we have files to zips
        if(count($valid_files)) {
            // echo "ok12";
            //create the archive
            $zip = new \ZipArchive();
            if($zip->open($destination,$overwrite ? \ZIPARCHIVE::OVERWRITE : \ZIPARCHIVE::CREATE) !== true) {
                return false;
            }
            //add the files
            foreach($valid_files as $file) {
                $zip->addFile($file,basename($file));
            }
            //close the zip -- done!
            $zip->close();

            //check to make sure the file exists
            header('Content-Type: application/zip');
            header('Content-disposition: attachment; filename='.$destination);
            header('Content-Length: ' . filesize($destination));
            readfile($destination);
            unlink($destination);
            
        }
    }
    

}