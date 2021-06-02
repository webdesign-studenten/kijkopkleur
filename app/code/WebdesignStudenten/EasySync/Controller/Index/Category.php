<?php
namespace WebdesignStudenten\EasySync\Controller\Index;

class Category extends \Magento\Framework\App\Action\Action {

    protected $categoryRepository;
    protected $resultRedirect;
        /**
     * Customer registry.
     *
     * @var \Magento\Customer\Model\CustomerRegistry
     */
    protected $_customerRegistry;
    public function __construct (
        \Magento\Framework\App\Action\Context $context,
        \Magento\Catalog\Model\CategoryRepository  $categoryRepository
    ){
        parent::__construct($context);
        $this->categoryRepository = $categoryRepository;
    }
	public function execute(){
    echo 'hello';
    // $category = $this->categoryRepository->get($categoryID);
    // return json_encode($category->getData());

	}
}
 ?>
