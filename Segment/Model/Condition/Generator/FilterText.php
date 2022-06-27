<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Segment\Model\Generator;

use Magento\AdvancedRule\Model\Condition\FilterTextGeneratorInterface;

class FilterText implements FilterTextGeneratorInterface
{
    /**
     * @param \Magento\Framework\DataObject $quoteAddress
     * @return string[]
     */
    public function generateFilterText(\Magento\Framework\DataObject $quoteAddress)
    {
        $filterText = [];
        if ($quoteAddress instanceof \Magento\Quote\Model\Quote\Address) {
            $customerSegmentIds = [1, 'segment1', 3];
            foreach ($customerSegmentIds as $customerSegmentId) {
                $text = \Magento\Segment\Model\Condition\Generator::FILTER_TEXT_PREFIX . $customerSegmentId;
                if (!in_array($text, $filterText)) {
                    $filterText[] = $text;
                }
            }
        }
        return $filterText;
    }
}
