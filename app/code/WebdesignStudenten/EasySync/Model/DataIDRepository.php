<?php
/**
 * Admin can sync customer, products, sales, cart, newsletter subscribers, wishlist etc.
 * Copyright (C) 2019  
 * 
 * This file is part of WebdesignStudenten/EasySync.
 * 
 * WebdesignStudenten/EasySync is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace WebdesignStudenten\EasySync\Model;

use WebdesignStudenten\EasySync\Api\DataIDRepositoryInterface;
use WebdesignStudenten\EasySync\Api\Data\DataIDSearchResultsInterfaceFactory;
use WebdesignStudenten\EasySync\Api\Data\DataIDInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use WebdesignStudenten\EasySync\Model\ResourceModel\DataID as ResourceDataID;
use WebdesignStudenten\EasySync\Model\ResourceModel\DataID\CollectionFactory as DataIDCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\ExtensibleDataObjectConverter;

class DataIDRepository implements DataIDRepositoryInterface
{

    protected $resource;

    protected $dataIDFactory;

    protected $dataIDCollectionFactory;

    protected $searchResultsFactory;

    protected $dataObjectHelper;

    protected $dataObjectProcessor;

    protected $dataDataIDFactory;

    protected $extensionAttributesJoinProcessor;

    private $storeManager;

    private $collectionProcessor;

    protected $extensibleDataObjectConverter;

    /**
     * @param ResourceDataID $resource
     * @param DataIDFactory $dataIDFactory
     * @param DataIDInterfaceFactory $dataDataIDFactory
     * @param DataIDCollectionFactory $dataIDCollectionFactory
     * @param DataIDSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceDataID $resource,
        DataIDFactory $dataIDFactory,
        DataIDInterfaceFactory $dataDataIDFactory,
        DataIDCollectionFactory $dataIDCollectionFactory,
        DataIDSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->dataIDFactory = $dataIDFactory;
        $this->dataIDCollectionFactory = $dataIDCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataDataIDFactory = $dataDataIDFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \WebdesignStudenten\EasySync\Api\Data\DataIDInterface $dataID
    ) {
        /* if (empty($dataID->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $dataID->setStoreId($storeId);
        } */
        
        $dataIDData = $this->extensibleDataObjectConverter->toNestedArray(
            $dataID,
            [],
            \WebdesignStudenten\EasySync\Api\Data\DataIDInterface::class
        );
        
        $dataIDModel = $this->dataIDFactory->create()->setData($dataIDData);
        
        try {
            $this->resource->save($dataIDModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the dataID: %1',
                $exception->getMessage()
            ));
        }
        return $dataIDModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getById($dataIDId)
    {
        $dataID = $this->dataIDFactory->create();
        $this->resource->load($dataID, $dataIDId);
        if (!$dataID->getId()) {
            throw new NoSuchEntityException(__('dataID with id "%1" does not exist.', $dataIDId));
        }
        return $dataID->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->dataIDCollectionFactory->create();
        
        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \WebdesignStudenten\EasySync\Api\Data\DataIDInterface::class
        );
        
        $this->collectionProcessor->process($criteria, $collection);
        
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        
        $items = [];
        foreach ($collection as $model) {
            $items[] = $model->getDataModel();
        }
        
        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \WebdesignStudenten\EasySync\Api\Data\DataIDInterface $dataID
    ) {
        try {
            $dataIDModel = $this->dataIDFactory->create();
            $this->resource->load($dataIDModel, $dataID->getDataidId());
            $this->resource->delete($dataIDModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the dataID: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($dataIDId)
    {
        return $this->delete($this->getById($dataIDId));
    }
}
