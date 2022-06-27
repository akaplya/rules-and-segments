<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Segment\Model\Data;

use Magento\Framework\DataObject;

class Segment extends DataObject
{
    const ID_FIELD_NAME = 'segment_id';

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->_getData(self::ID_FIELD_NAME);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setId(string $value): Segment
    {
        $this->setData(self::ID_FIELD_NAME, $value);
        return $this;
    }
}
