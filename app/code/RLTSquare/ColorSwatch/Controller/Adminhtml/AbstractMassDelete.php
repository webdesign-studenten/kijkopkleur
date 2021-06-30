<?php
/**
 *
 * @category : RLTSquare
 * @Package  : RLTSquare_ColorSwatch
 * @Author   : RLTSquare <support@rltsquare.com>
 * @copyright Copyright 2021 © rltsquare.com All right reserved
 * @license https://rltsquare.com/
 */
namespace RLTSquare\ColorSwatch\Controller\Adminhtml;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
/**
 * Class AbstractMassDelete
 */
class AbstractMassDelete extends \Magento\Backend\App\Action
{
    /**
     * Redirect url
     */
    const REDIRECT_URL = '*/*/';
    protected $ob;
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Catalog\Controller\Adminhtml\Product\Builder $productBuilder,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->filesystem = $filesystem;
        $this->ob=$this->_objectManager;
    }
    /**
     * Resource collection
     *
     * @var string
     */
    protected $collection = 'Magento\Framework\Model\Resource\Db\Collection\AbstractCollection';
    /**
     * Model
     *
     * @var string
     */
    protected $model = 'Magento\Framework\Model\AbstractModel';
    protected $cat = false;
    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $selected = $this->getRequest()->getParam('selected');
        $excluded = $this->getRequest()->getParam('excluded');
        try {
            if (isset($excluded)) {
                if ($excluded!='false') {
                    $this->excludedDelete($excluded);
                } else {
                    $this->deleteAll();
                }
            } elseif (!empty($selected)) {
                $this->selectedDelete($selected);
            } else {
                $this->messageManager->addError(__('Please select item(s).'));
            }
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath(static::REDIRECT_URL);
    }
    /**
     * Delete all
     *
     * @return void
     * @throws \Exception
     */
    protected function deleteAll()
    {
        /** @var AbstractCollection $collection */
        $collection = $this->ob->get($this->collection);
        $this->delete($collection);
    }
    /**
     * Delete all but the not selected
     *
     * @param array $excluded
     * @return void
     * @throws \Exception
     */
    protected function excludedDelete(array $excluded)
    {
        /** @var AbstractCollection $collection */
        $collection = $this->ob->get($this->collection);
        $collection->addFieldToFilter(static::ID_FIELD, ['nin' => $excluded]);
        $this->delete($collection);
    }
    /**
     * Delete selected items
     *
     * @param array $selected
     * @return void
     * @throws \Exception
     */
    protected function selectedDelete(array $selected)
    {
        /** @var AbstractCollection $collection */
        $collection = $this->ob->get($this->collection);
        $collection->addFieldToFilter(static::ID_FIELD, ['in' => $selected]);
        $this->delete($collection);
    }
    /**
     * Delete collection items
     *
     * @param AbstractCollection $collection
     * @return int
     */
    protected function delete(AbstractCollection $collection)
    {
        $count = 0;
        $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $mediaRootDir = $mediaDirectory->getAbsolutePath();
        foreach ($collection->getAllIds() as $id) {
            /** @var \Magento\Framework\Model\AbstractModel $model */
            $model = $this->ob->get($this->model);
            $model->load($id);
            $model->delete();
            ++$count;
        }
        $this->setSuccessMessage($count);
        return $count;
    }
    /**
     * Set error messages
     *
     * @param int $count
     * @return void
     */
    protected function setSuccessMessage($count)
    {
        $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $count));
    }
    protected function _isAllowed()
    {
        return true;
    }
}
