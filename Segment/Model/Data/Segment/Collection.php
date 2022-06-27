<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Segment\Model\Data\Segment;

use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\Data\Collection as FrameworkCollection;
use Magento\Segment\Model\Data\Segment;

class Collection extends FrameworkCollection
{
    /**
     * @var DataProvider
     */
    private $dataProvider;

    /**
     * @param EntityFactoryInterface $entityFactory
     * @param DataProvider $dataProvider
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        DataProvider $dataProvider
    ) {
        parent::__construct($entityFactory);
        $this->dataProvider = $dataProvider;
    }

    /**
     * Item object class name
     *
     * @var string
     */
    protected $_itemObjectClass = Segment::class;

    /**
     * Load data
     *
     * @param bool $printQuery
     * @param bool $logQuery
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function loadData($printQuery = false, $logQuery = false)
    {
        if (!$this->isLoaded()) {
            foreach ($this->dataProvider->getSegmentsData() as $segmentData) {
                $obj = $this->_entityFactory->create($this->_itemObjectClass);
                $obj->setData($segmentData);
                $this->addItem($obj);
            }
            $this->_setIsLoaded();
        }
        return $this;
    }
}
