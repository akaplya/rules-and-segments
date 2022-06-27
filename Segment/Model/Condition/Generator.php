<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Segment\Model\Condition;

use Magento\AdvancedRule\Model\Condition\FilterGroupInterface;
use Magento\AdvancedRule\Model\Condition\FilterInterface;
use Magento\AdvancedRule\Model\Condition\FilterGroupInterfaceFactory;
use Magento\Segment\Model\Generator\FilterText;
use Magento\AdvancedRule\Helper\Filter as FilterHelper;

/**
 * For the given Segment rule condition, supplies the contents that go into the table used to determine
 * which cart sales rules should be further evaluated.
 */
class Generator
{
    const FILTER_TEXT_PREFIX = 'reference:segment:';
    const FILTER_TEXT_GENERATOR_CLASS = FilterText::class;

    /**
     * @var FilterHelper
     */
    protected $filterHelper;

    /**
     * @var FilterGroupInterfaceFactory
     */
    protected $filterGroupFactory;

    public function __construct(
        FilterGroupInterfaceFactory $filterGroupFactory,
        FilterHelper $filterHelper
    ) {
        $this->filterGroupFactory = $filterGroupFactory;
        $this->filterHelper = $filterHelper;
    }

    /**
     * Return a list of filter groups that represent this condition
     *
     * @param string $operator
     * @param string $value
     * @return FilterGroupInterface[]
     */
    public function getFilterGroups(string $operator, string $value): array
    {
        $segmentIds = explode(',', str_replace(' ', '', $value));
        $weight = 1;

        $negativeFilters = [];
        $negativeCondition = false;
        if ($operator == '!=' || $operator == '!()') {
            $negativeCondition = true;
            $weight = -1;
        }

        $filterGroups = [];
        foreach ($segmentIds as $segmentId) {
            /** @var FilterInterface $filter */
            $filter = $this->filterHelper->createFilter();
            $filter->setFilterText(self::FILTER_TEXT_PREFIX . $segmentId)
                ->setWeight($weight)
                ->setFilterTextGeneratorClass(self::FILTER_TEXT_GENERATOR_CLASS)
                ->setFilterTextGeneratorArguments(json_encode([]));

            if ($negativeCondition) {
                // we will accumulate all negative filters into one group (logical 'and')
                $negativeFilters[] = $filter;
            } else {
                // we accumulate all positive filters into separate groups (logical 'or')
                /** @var FilterGroupInterface $filterGroup */
                $filterGroup = $this->filterGroupFactory->create();
                $filterGroup->setFilters([$filter]);
                $filterGroups[] = $filterGroup;
            }
        }

        if ($negativeCondition && !empty($negativeFilters)) {
            // add in the 'true' filter (which has a +1 weight).
            // This will cause the rule to be a candidate only if none of the negative conditions are met.
            $negativeFilters[] = $this->filterHelper->getFilterTrue();

            /** @var FilterGroupInterface $filterGroup */
            $filterGroup = $this->filterGroupFactory->create();
            $filterGroup->setFilters($negativeFilters);
            $filterGroups[] = $filterGroup;
        }
        return $filterGroups;
    }
}
