<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_PDFCustom
 */


namespace Amasty\PDFCustom\Model\ResourceModel;

use Amasty\PDFCustom\Model\Source\PlaceForUse;

class TemplateRepository
{
    /**
     * @var Template
     */
    private $resource;

    /**
     * @var array
     */
    private $templatesByParams = [];

    public function __construct(Template $resource)
    {
        $this->resource = $resource;
    }

    /**
     * @param int $storeId
     * @param int $customerGroupId
     *
     * @return int
     */
    public function getOrderTemplateId($storeId, $customerGroupId)
    {
        return $this->getTemplateIdByParams(
            PlaceForUse::TYPE_ORDER,
            $storeId,
            $customerGroupId
        );
    }

    /**
     * @param int $storeId
     * @param int $customerGroupId
     *
     * @return int
     */
    public function getInvoiceTemplateId($storeId, $customerGroupId)
    {
        return $this->getTemplateIdByParams(
            PlaceForUse::TYPE_INVOICE,
            $storeId,
            $customerGroupId
        );
    }

    /**
     * @param int $storeId
     * @param int $customerGroupId
     *
     * @return int
     */
    public function getShipmentTemplateId($storeId, $customerGroupId)
    {
        return $this->getTemplateIdByParams(
            PlaceForUse::TYPE_SHIPPING,
            $storeId,
            $customerGroupId
        );
    }

    /**
     * @param int $storeId
     * @param int $customerGroupId
     *
     * @return int
     */
    public function getCreditmemoTemplateId($storeId, $customerGroupId)
    {
        return $this->getTemplateIdByParams(
            PlaceForUse::TYPE_CREDIT_MEMO,
            $storeId,
            $customerGroupId
        );
    }

    /**
     * @param int $placeForUse
     * @param int $storeId
     * @param int $customerGroupId
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getTemplateIdByParams($placeForUse, $storeId, $customerGroupId)
    {
        $cacheKey = $this->getCacheKey([$placeForUse, $storeId, $customerGroupId]);
        if (array_key_exists($cacheKey, $this->templatesByParams)) {
            return $this->templatesByParams[$cacheKey];
        }
        $templates = $this->resource->getTemplatesDataByPlace($placeForUse);
        $templateCandidatesWithPriority = [];
        foreach ($templates as $template) {
            $storeIds = !empty($template['store_ids']) ? explode(',', $template['store_ids']) : [];
            $customerGroupIds = !empty($template['customer_group_ids']) ?
                explode(',', $template['customer_group_ids']) :
                [];

            if ( // store view & customer group
                in_array($storeId, $storeIds) &&
                in_array($customerGroupId, $customerGroupIds)
            ) {
                $templateCandidatesWithPriority[0][] = $template['template_id'];
            } elseif ( // store view & all customer groups
                in_array($storeId, $storeIds) &&
                empty($customerGroupIds)
            ) {
                $templateCandidatesWithPriority[1][] = $template['template_id'];
            } elseif ( // all store views & customer group
                (in_array(0, $storeIds) || empty($storeIds)) &&
                in_array($customerGroupId, $customerGroupIds)
            ) {
                $templateCandidatesWithPriority[2][] = $template['template_id'];
            } elseif ( // all store views & all customer groups
                (in_array(0, $storeIds) || empty($storeIds)) &&
                empty($customerGroupIds)
            ) {
                $templateCandidatesWithPriority[3][] = $template['template_id'];
            }
        }

        ksort($templateCandidatesWithPriority);
        $templateCandidates = current($templateCandidatesWithPriority);

        $resultTemplateId = 0;
        if ($templateCandidates) {
            $resultTemplateId = min($templateCandidates);
        }
        $this->templatesByParams[$cacheKey] = $resultTemplateId;

        return $resultTemplateId;
    }

    /**
     * @param array $params
     * @return string
     */
    private function getCacheKey(array $params)
    {
        return implode('_', $params);
    }
}
