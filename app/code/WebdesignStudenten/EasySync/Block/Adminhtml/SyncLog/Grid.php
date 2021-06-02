<?php
namespace WebdesignStudenten\EasySync\Block\Adminhtml\SyncLog;


class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory]
     */
    protected $_setsFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var \Magento\Catalog\Model\Product\Type
     */
    protected $_type;

    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Source\Status
     */
    protected $_status;
	protected $_collectionFactory;

    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $_visibility;

    /**
     * @var \Magento\Store\Model\WebsiteFactory
     */
    protected $_websiteFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Store\Model\WebsiteFactory $websiteFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $setsFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Model\Product\Type $type
     * @param \Magento\Catalog\Model\Product\Attribute\Source\Status $status
     * @param \Magento\Catalog\Model\Product\Visibility $visibility
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Store\Model\WebsiteFactory $websiteFactory,
		\WebdesignStudenten\EasySync\Model\ResourceModel\SyncLog\Collection $collectionFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
		
		$this->_collectionFactory = $collectionFactory;
        $this->_websiteFactory = $websiteFactory;
        $this->moduleManager = $moduleManager;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
		
        $this->setId('data_sync_id');
        $this->setDefaultSort('data_sync_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(false);
       
    }

    /**
     * @return Store
     */
    protected function _getStore()
    {
        $storeId = (int)$this->getRequest()->getParam('store', 0);
        return $this->_storeManager->getStore($storeId);
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
		try{
			
			
			$collection =$this->_collectionFactory->load();

		  

			$this->setCollection($collection);

			parent::_prepareCollection();
		  
			return $this;
		}
		catch(Exception $e)
		{
			echo $e->getMessage();die;
		}
    }

    /**
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'data_sync_id',
            [
                'header' => __('Sync ID'),
                'type' => 'number',
                'index' => 'data_sync_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
		$this->addColumn(
            'dataScope',
            [
                'header' => __('Data Scope'),
                'index' => 'dataScope',
                'class' => 'dataScope'
            ]
        );
        $this->addColumn(
            'dataID',
            [
                'header' => __('Data ID'),
                'type' => 'number',
                'index' => 'dataID',
                'class' => 'dataID'
            ]
        );
        $this->addColumn(
            'LogType',
            [
                'header' => __('Log Type'),
                'index' => 'LogType',
                'class' => 'LogType'
            ]
        );
        $this->addColumn(
            'ChangeLog',
            [
                'header' => __('Change Log'),
                'index' => 'ChangeLog',
                'class' => 'ChangeLog',
                'size' => '200px',
                'renderer' => 'WebdesignStudenten\EasySync\Block\Adminhtml\SyncLog\FormatChangeLog'
            ]
        );
        
        $this->addColumn(
            'OldValue',
            [
                'header' => __('Old Value'),
                'index' => 'OldValue',
                'class' => 'OldValue',
                'size' => '200px',
                'renderer' => 'WebdesignStudenten\EasySync\Block\Adminhtml\SyncLog\FormatOldLog'
            ]
        );
		$this->addColumn(
            'UpdateDate',
            [
                'header' => __('Update Date'),
                'index' => 'UpdateDate',
                'class' => 'UpdateDate'
            ]
        );
        
        $this->addColumn(
            'UpdateFlag',
            [
                'header' => __('Update Flag'),
                'type' => 'number',
                'index' => 'UpdateFlag',
                'class' => 'UpdateFlag',
                'renderer' => 'WebdesignStudenten\EasySync\Block\Adminhtml\SyncLog\FormatUpdateFlag'
            ]
        );
        

        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }

        return parent::_prepareColumns();
    }
}
