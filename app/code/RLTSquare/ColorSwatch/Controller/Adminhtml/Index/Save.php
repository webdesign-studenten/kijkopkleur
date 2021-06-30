<?php
/**
 *
 * @category : RLTSquare
 * @Package  : RLTSquare_ColorSwatch
 * @Author   : RLTSquare <support@rltsquare.com>
 * @copyright Copyright 2021 © rltsquare.com All right reserved
 * @license https://rltsquare.com/
 */
namespace RLTSquare\ColorSwatch\Controller\Adminhtml\Index;
use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;
use RLTSquare\ColorSwatch\Model\ColorSwatch;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\TestFramework\Inspection\Exception;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\RequestInterface;
/**
 * Class Save
 * @package RLTSquare\ColorSwatch\Controller\Adminhtml\Index
 */
class Save extends \Magento\Backend\App\Action
{
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var \Magento\Framework\Escaper
     */
    protected $_escaper;
    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTimeFactory
     */
    protected $_dateFactory;
    /**
     * @var \Magento\Backend\Helper\Js
     */
    protected $_jsHelper;
    /**
     * @var ColorSwatch
     */
    protected $modelColorSwatch;
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resourceConnection;
    /**
     * @param Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param DataPersistorInterface $dataPersistor
     */
    public function __construct(
        Context $context,
        DataPersistorInterface $dataPersistor,
        \Magento\Framework\Escaper $escaper,
        \Magento\Backend\Helper\Js $jsHelper,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \RLTSquare\ColorSwatch\Model\ColorSwatch $modelColorSwatch,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Stdlib\DateTime\DateTimeFactory $dateFactory
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->scopeConfig = $scopeConfig;
        $this->_escaper = $escaper;
        $this->resourceConnection = $resourceConnection;
        $this->modelColorSwatch = $modelColorSwatch;
        $this->_jsHelper = $jsHelper;
        $this->_dateFactory = $dateFactory;
        $this->inlineTranslation = $inlineTranslation;
        parent::__construct($context);
    }
    /**
     * Save action
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $id = $this->getRequest()->getParam('colorswatch_id');
            if (isset($data['status']) && $data['status'] === 'true') {
                $data['status'] = Block::STATUS_ENABLED;
            }
            if (empty($data['colorswatch_id'])) {
                $data['colorswatch_id'] = null;
            }
            /** @var \Magento\Cms\Model\Block $model */
            $model = $this->modelColorSwatch->load($id);

            if (!$model->getId() && $id) {
                $this->messageManager->addError(__('This Color Shade no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }
            $model->setData($data);
            $this->inlineTranslation->suspend();
            try {
                $model->save();
                $this->saveProducts($model, $data);
                $this->messageManager->addSuccess(__('Color Shade Saved successfully'));
                $this->dataPersistor->clear('rlt_square_colorswatch');
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['colorswatch_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the Color Shade.'));
            }
            $this->dataPersistor->set('rlt_square_colorswatch', $data);
            return $resultRedirect->setPath('*/*/edit', ['colorswatch_id' => $this->getRequest()->getParam('colorswatch_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
    /**
     * @param $model
     * @param $post
     */
    public function saveProducts($model, $post)
    {
        // Attach the attachments to contact
        if (isset($post['products'])) {
            $productIds = $this->_jsHelper->decodeGridSerializedInput($post['products']);
            try {
                $oldProducts = (array) $model->getProducts($model);
                $newProducts = (array) $productIds;

                $connection = $this->resourceConnection->getConnection();

                $table = $this->resourceConnection->getTableName("rltsquare_colorswatch_products");
                $insert = array_diff($newProducts, $oldProducts);
                $delete = array_diff($oldProducts, $newProducts);

                if ($delete) {
                    $where = ['colorswatch_id = ?' => (int)$model->getId(), 'product_id IN (?)' => $delete];
                    $connection->delete($table, $where);
                }

                if ($insert) {
                    $data = [];
                    foreach ($insert as $product_id) {
                        $data[] = ['colorswatch_id' => (int)$model->getId(), 'product_id' => (int)$product_id];
                    }
                    $connection->insertMultiple($table, $data);
                }
            } catch (Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the contact.'));
            }
        }

    }
}
