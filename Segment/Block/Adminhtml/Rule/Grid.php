<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Segment\Block\Adminhtml\Rule;

use Magento\Segment\Model\Data\Segment\CollectionFactory;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        CollectionFactory $collectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $backendHelper, $data);
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Init grid
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setSaveParametersInSession(true);
        if ($this->getRequest()->getParam('current_grid_id')) {
            $this->setId($this->getRequest()->getParam('current_grid_id'));
        } else {
            $this->setId('segment_grid_chooser_' . $this->getId());
        }
        $this->setUseAjax(true);

        $form = $this->getRequest()->getParam('form');
        if ($form) {
            $this->setRowClickCallback("{$form}.chooserGridRowClick.bind({$form})");
            $this->setCheckboxCheckCallback("{$form}.chooserGridCheckboxCheck.bind({$form})");
            $this->setRowInitCallback("{$form}.chooserGridRowInit.bind({$form})");
        }
        if ($this->getRequest()->getParam('collapse')) {
            $this->setIsCollapsed(true);
        }
    }


    /**
     * @return $this|Grid
     */
    protected function _prepareCollection()
    {
        /** @var $collection \Magento\CustomerSegment\Model\ResourceModel\Segment\Collection */
        $collection = $this->collectionFactory->create();
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    /**
     * Add grid columns
     *
     * @return $this
     */
    protected function _prepareColumns()
    {
        // this column is mandatory for the chooser mode. It needs to be first
        $this->addColumn(
            'in_segments',
            [
                'header_css_class' => 'a-center',
                'type' => 'checkbox',
                'name' => 'in_segments',
                'values' => $this->_getSelectedSegments(),
                'align' => 'center',
                'index' => 'segment_id',
                'use_index' => true
            ]
        );
        $this->addColumn(
            'grid_segment_id',
            [
                'header' => __('ID'),
                'index' => 'segment_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            'grid_segment_name',
            [
                'header' => __('Segment'),
                'index' => 'name'
            ]
        );
        $this->addColumn(
            'grid_segment_type',
            [
                'header' => __('Type'),
                'index' => 'type'
            ]
        );
        parent::_prepareColumns();
        return $this;
    }

    /**
     * Retrieve row click URL
     *
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function getRowUrl($row)
    {
            return null;
    }


    /**
     * Row click javascript callback getter
     *
     * @return string
     */
    public function getRowClickCallback()
    {
        return $this->_getData('row_click_callback');
    }

    /**
     * Get Selected ids param from request
     *
     * @return array
     */
    protected function _getSelectedSegments()
    {
        $segments = $this->getRequest()->getPost('selected', []);
        return $segments;
    }

    /**
     * Grid URL getter for ajax mode
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('segment/rule/grid', ['_current' => true]);
    }
}
