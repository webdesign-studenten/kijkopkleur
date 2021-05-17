<?php

namespace Keizer\Productattach\Model\Product\Attribute\Backend;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend;

/**
 * Class File
 * @package Keizer\Productattach\Model\Product\Attribute\Backend
 */
class File extends AbstractBackend
{
    const MEDIA_PATH    = 'productattach';

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    protected $_file;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_filesystem;

    /**
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $_fileUploaderFactory;

    /**
     * @var \Magenuts\Productattach\Model\ResourceModel\Productattach\CollectionFactory
     */
    private $_productattachCollectionFactory;

    /**
     * @var Magenuts\Productattach\Model\ProductattachFactory
     */
    private $_productattachFactory;

    /**
     * Customer Group
     *
     * @var \Magento\Customer\Model\ResourceModel\Group\Collection
     */
    protected $_customerGroup;

    /**
     * @var \Magenuts\Productattach\Helper\Data
     */
    private $_helper;

    /**
     * @var \Magento\Store\Api\StoreRepositoryInterface
     */
    protected $_storeRepository;

    /**
     * File constructor.
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Filesystem\Driver\File $file
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
     * @param \Magenuts\Productattach\Model\ResourceModel\Productattach\CollectionFactory $productattachCollectionFactory
     * @param \Magenuts\Productattach\Model\ProductattachFactory $productattachFactory
     * @param \Magento\Customer\Model\ResourceModel\Group\Collection $customerGroup
     * @param \Magenuts\Productattach\Helper\Data $helper
     * @param \Magento\Store\Api\StoreRepositoryInterface $storeRepository
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Filesystem\Driver\File $file,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magenuts\Productattach\Model\ResourceModel\Productattach\CollectionFactory $productattachCollectionFactory,
        \Magenuts\Productattach\Model\ProductattachFactory $productattachFactory,
        \Magento\Customer\Model\ResourceModel\Group\Collection $customerGroup,
        \Magenuts\Productattach\Helper\Data $helper,
        \Magento\Store\Api\StoreRepositoryInterface $storeRepository
    )
    {
        $this->_file = $file;
        $this->_filesystem = $filesystem;
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->_logger = $logger;
        $this->_productattachCollectionFactory = $productattachCollectionFactory;
        $this->_productattachFactory = $productattachFactory;
        $this->_customerGroup = $customerGroup;
        $this->_helper = $helper;
        $this->_storeRepository = $storeRepository;
    }

    /**
     * @param \Magento\Framework\DataObject $object
     * @return $this|File
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function afterSave($object)
    {
        $path = $this->getBaseDir();
        $delete = $object->getData($this->getAttribute()->getName() . '_delete');

        if ($delete) {
            $fileName = $object->getData($this->getAttribute()->getName());
            $object->setData($this->getAttribute()->getName(), '');
            $this->deleteAttachment($fileName);
            $this->getAttribute()->getEntity()->saveAttribute($object, $this->getAttribute()->getName());
            if ($this->_file->isExists($path . $fileName)) {
                $this->_file->deleteFile($path . $fileName);
            }
        }
        $this->saveProductAttachmentAttribute($object);
        if (empty($_FILES['product']['tmp_name'][$this->getAttribute()->getName()])) {
            return $this;
        }

        try {
            /** @var $uploader \Magento\MediaStorage\Model\File\Uploader */
            $uploader = $this->_fileUploaderFactory->create(['fileId' => 'product[' . $this->getAttribute()->getName() . ']']);
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);
            $uploader->setAllowCreateFolders(true);
            $result = $uploader->save($path);
            $object->setData($this->getAttribute()->getName(), $result['file']);
            $this->getAttribute()->getEntity()->saveAttribute($object, $this->getAttribute()->getName());
            /** upload in attachment table*/
            $fileName = $uploader->getUploadedFileName();
            $fileExt = $uploader->getFileExtension();
            $this->saveAttachment($object, $fileName, $fileExt);
        } catch (\Exception $e) {
            $this->_logger->info($e->getMessage());
        }
        return $this;
    }

    /**
     * Return the base media directory for Productattach Item images
     *
     * @return string
     */
    public function getBaseDir()
    {
        return $this->_filesystem->getDirectoryRead(
            DirectoryList::MEDIA
        )->getAbsolutePath(self::MEDIA_PATH);
    }

    /**
     * @param \Magento\Framework\DataObject $object
     * @param $fileName
     * @param $fileExt
     * @return bool
     */
    private function saveAttachment($object, $fileName, $fileExt){
        $storeId = $object->getData('store_id');
        if($storeId==0){
            $storeId = $this->getStoreList();
        }
        $productId = $object->getData('entity_id');
        $attachmentName = $object->getData('attachment_name');
        $url = $object->getData('attachment_url');
        $attachmentStatus = ($object->getData('attachment_status')=="") ? 1 : $object->getData('attachment_status');
        $allCustomerGroup = $this->getAllCustomerGroup();
        $collection = $this->_productattachCollectionFactory->create();
        $collection->getSelect()->where("store LIKE '%".$storeId."%'");
        $collection->getSelect()->where("products REGEXP '[[:<:]]".$productId."[[:>:]]'");
        $attachment = $collection->getFirstItem();
        if(empty($attachment->getData())){
            $data = [
                "name" => $attachmentName,
                "store" => $storeId,
                "file" => $fileName,
                "file_ext" => $fileExt,
                "url" => $url,
                "customer_group" => $allCustomerGroup,
                "products" => $productId,
                "active" => $attachmentStatus
            ];
            $this->_productattachFactory->create()
                ->setData($data)->save();
        }
        else{
            $attachment->setName($attachmentName);
            $attachment->setFile($fileName);
            $attachment->setFileExt($fileExt);
            $attachment->setUrl($url);
            $attachment->setActive($attachmentStatus);
            $attachment->save();
        }
        return true;
    }

    /**
     * @param $object
     * @return bool
     */
    public function saveProductAttachmentAttribute($object){
        $storeId = $object->getData('store_id');
        if($storeId==0){
            $storeId = $this->getStoreList();
        }
        $productId = $object->getData('entity_id');
        $attachmentName = $object->getData('attachment_name');
        $url = $object->getData('attachment_url');
        $attachmentStatus = ($object->getData('attachment_status')=="") ? 1 : $object->getData('attachment_status');
        $allCustomerGroup = $this->getAllCustomerGroup();
        $collection = $this->_productattachCollectionFactory->create();
        $collection->getSelect()->where("store LIKE '%".$storeId."%'");
        $collection->getSelect()->where("products REGEXP '[[:<:]]".$productId."[[:>:]]'");
        $attachment = $collection->getFirstItem();
        if(empty($attachment->getData())){
            $data = [
                "name" => $attachmentName,
                "store" => $storeId,
                "url" => $url,
                "customer_group" => $allCustomerGroup,
                "products" => $productId,
                "active" => $attachmentStatus
            ];
            $this->_productattachFactory->create()
                ->setData($data)->save();
        }
        else{
            $attachment->setName($attachmentName);
            $attachment->setUrl($url);
            $attachment->setActive($attachmentStatus);
            $attachment->save();
        }
        return true;
    }

    /**
     * @return string
     */
    public function getStoreList(){
        $list = $this->_storeRepository->getList();
        $storeList = [];
        foreach ($list as $_list){
            $storeList[] = $_list->getId();
        }
        return implode(',', $storeList);
    }

    /**
     * @return string
     */
    private function getAllCustomerGroup(){
        $allGroupCollection = $this->_customerGroup->toOptionArray();
        $allGroup = [];
        foreach($allGroupCollection as $_group){
            $allGroup[] = $_group['value'];
        }
        return implode(',', $allGroup);
    }

    /**
     * @param string $fileName
     * @return bool
     */
    private function deleteAttachment($fileName){
        try {
            $collection = $this->_productattachCollectionFactory->create()->addFieldToFilter('file', $fileName);
            foreach($collection as $_collection){
                $_collection->setFile('');
                $_collection->save();
            }
        }
        catch (\Exception $ex){
            $this->_logger->info($ex->getMessage());
        }
        return true;
    }
}