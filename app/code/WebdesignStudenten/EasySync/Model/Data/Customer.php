<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace WebdesignStudenten\EasySync\Model\Data;

use \Magento\Framework\Api\AttributeValueFactory;

/**
 * Class Customer
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class Customer extends \Magento\Customer\Model\Data\Customer implements
    \WebdesignStudenten\EasySync\Api\Data\CustomerInterface
{
    
    /**
     * Get Password Hash
     *
     * @return string
     */
    public function getPasswordHash()
    {
        return $this->_get(self::PASSWORD_HASH);
    }

    /**
     * Get Rp Token
     *
     * @return string
     */
    public function getRpToken()
    {
        return $this->_get(self::RP_TOKEN);
    }

    /**
     * Get Rp Token Created At
     *
     * @return string|null
     */
    public function getRpTokenCreatedAt()
    {
        return $this->_get(self::RP_TOKEN_CREATED_AT);
    }
    
    /**
     * Set first name
     *
     * @param string $passwordHash
     * @return $this
     */
    public function setPasswordHash($passwordHash)
    {
        return $this->setData(self::PASSWORD_HASH, $passwordHash);
    }
    
    /**
     * Set first name
     *
     * @param string $rpToken
     * @return $this
     */
    public function setRpToken($rpToken)
    {
        return $this->setData(self::RP_TOKEN, $rpToken);
    }
    
    /**
     * Set first name
     *
     * @param string $rpTokenCreatedAt
     * @return $this
     */
    public function setRpTokenCreatedAt($rpTokenCreatedAt)
    {
        return $this->setData(self::RP_TOKEN_CREATED_AT, $rpTokenCreatedAt);
    }

}
