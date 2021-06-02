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

namespace WebdesignStudenten\EasySync\Model\Data;

use WebdesignStudenten\EasySync\Api\Data\DataIDInterface;

class DataID extends \Magento\Framework\Api\AbstractExtensibleObject implements DataIDInterface
{

    /**
     * Get dataid_id
     * @return string|null
     */
    public function getDataidId()
    {
        return $this->_get(self::DATAID_ID);
    }

    /**
     * Set dataid_id
     * @param string $dataidId
     * @return \WebdesignStudenten\EasySync\Api\Data\DataIDInterface
     */
    public function setDataidId($dataidId)
    {
        return $this->setData(self::DATAID_ID, $dataidId);
    }

    /**
     * Get dataID
     * @return string|null
     */
    public function getDataID()
    {
        return $this->_get(self::DATAID);
    }

    /**
     * Set dataID
     * @param string $dataID
     * @return \WebdesignStudenten\EasySync\Api\Data\DataIDInterface
     */
    public function setDataID($dataID)
    {
        return $this->setData(self::DATAID, $dataID);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \WebdesignStudenten\EasySync\Api\Data\DataIDExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \WebdesignStudenten\EasySync\Api\Data\DataIDExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \WebdesignStudenten\EasySync\Api\Data\DataIDExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
