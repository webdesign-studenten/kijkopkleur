<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace WebdesignStudenten\EasySync\Api\Data;

/**
 * Customer interface.
 * @api
 * @since 100.0.2
 */
interface CustomerInterface extends \Magento\Customer\Api\Data\CustomerInterface
{
    const PASSWORD_HASH = 'password_hash';
    const RP_TOKEN = 'rp_token';
    const RP_TOKEN_CREATED_AT = 'rp_token_created_at';
    /**
     * Get PasswordHash
     *
     * @return string
     */
    public function getPasswordHash();

    /**
     * Set PasswordHash
     *
     * @param string $passwordHash
     * @return $this
     */
    public function setPasswordHash($passwordHash);
    
    /**
     * Get RpToken
     *
     * @return string
     */
    public function getRpToken();

    /**
     * Set RpToken
     *
     * @param string $rpToken
     * @return $this
     */
    public function setRpToken($rpToken);
    /**
     * Get RpToken CreatedAt
     *
     * @return string
     */
    public function getRpTokenCreatedAt();

    /**
     * Set RpToken CreatedAt
     *
     * @param string $rpTokenCreatedAt
     * @return $this
     */
    public function setRpTokenCreatedAt($rpTokenCreatedAt);
}
