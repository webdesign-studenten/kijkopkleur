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

namespace Milople\Vatexempt\Api\Data;

interface ConditionsInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case.
     */
    const CONDITION_ID = 'condition_id';
    const CONDITION_NAME = 'condition_name';
    const STATUS = 'status';
    const CREATED_AT = 'created_at';
    const PUBLISHED_AT = 'published_at';

    /**
     * Get condition_id.
     *
     * @return int
     */
    public function getConditionId();
    /**
     * Set condition_id.
     */
    public function setConditionId($condition_id);

    /**
     * Get condition_name.
     *
     * @return varchar
     */
    public function getConditionName();
    /**
     * Set condition_name.
     */
    public function setConditionName($condition_name);

    /**
     * Get status.
     *
     * @return varchar
     */
    public function getStatus();
    /**
     * Set status.
     */
    public function setStatus($status);

    /**
     * Get created_at.
     *
     * @return varchar
     */
    public function getCreatedAt();

    /**
     * Set created_at.
     */
    public function setCreatedAt($created_at);

    /**
     * Get published_at.
     *
     * @return varchar
     */
    public function getPublishedAt();

    /**
     * Set published_at.
     */
    public function setPublishedAt($published_at);
}