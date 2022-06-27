<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Segment\Observer;

use Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\AuthorizationInterface;

/**
 * Class for adding Customer Segment conditions section
 */
class AddSegmentsToSalesRuleCombineObserver implements ObserverInterface
{
    /**
     * @var AuthorizationInterface
     */
    private $authorization;

    /**
     * @param AuthorizationInterface $authorization
     */
    public function __construct(
        AuthorizationInterface $authorization
    ) {
        $this->authorization = $authorization;
    }

    /**
     * Add Customer Segment condition to the salesrule management
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->authorization->isAllowed('Magento_Segment::segment')) {
            return;
        }

        $additional = $observer->getEvent()->getAdditional();
        $conditions = (array) $additional->getConditions();
        $conditions = array_merge_recursive(
            $conditions,
            [
                [
                    'label' => __('Segments Reference Implementation Segment'),
                    'value' => \Magento\Segment\Model\Condition::class,
                ],
            ]
        );
        $additional->setConditions($conditions);
    }
}
