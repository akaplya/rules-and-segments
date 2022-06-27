<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Segment\Model;

use Magento\AdvancedRule\Model\Condition\FilterGroupInterface;
use Magento\Segment\Model\Condition\Generator;
use Magento\Rule\Model\Condition\AbstractCondition;
use Magento\AdvancedRule\Model\Condition\FilterableConditionInterface;
use Magento\Rule\Model\Condition\Context as ConditionContext;
use Magento\Backend\Helper\Data as BackendHelper;


/**
 * Segment condition for sales rules
 */
class Condition extends AbstractCondition implements FilterableConditionInterface
{
    /**
     * @var string
     */
    protected $_inputType = 'multiselect';

    /**
     * Adminhtml data
     *
     * @var \Magento\Backend\Helper\Data
     */
    private $_adminhtmlData;

    /**
     * @var \Magento\Framework\Data\Form\Element\AbstractElement
     */
    private $_valueElement;

    /**
     * @var \Magento\Segment\Model\SegmentResolver
     */
    private $segmentResolver;
    /**
     * @var Generator
     */
    private $generator;

    /**
     * @param SegmentResolver $segmentResolver
     * @param ConditionContext $context
     * @param BackendHelper $adminhtmlData
     * @param Generator $generator
     * @param array $data
     */
    public function __construct(
        SegmentResolver $segmentResolver,
        ConditionContext $context,
        BackendHelper $adminhtmlData,
        Generator $generator,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_adminhtmlData = $adminhtmlData;
        $this->segmentResolver = $segmentResolver;
        $this->generator = $generator;
    }

    /**
     * Default operator input by type map getter
     *
     * @return array
     */
    public function getDefaultOperatorInputByType()
    {
        if (null === $this->_defaultOperatorInputByType) {
            $this->_defaultOperatorInputByType = ['multiselect' => ['==', '!=', '()', '!()']];
            $this->_arrayInputTypes = ['multiselect'];
        }
        return $this->_defaultOperatorInputByType;
    }

    /**
     * Render chooser trigger
     *
     * @return string
     */
    public function getValueAfterElementHtml()
    {
        return '<a href="javascript:void(0)" class="rule-chooser-trigger"><img src="' .
            $this->_assetRepo->getUrl(
                'images/rule_chooser_trigger.gif'
            ) . '" alt="" class="v-middle rule-chooser-trigger" title="' . __(
                'Open Chooser'
            ) . '" /></a>';
    }

    /**
     * Value element type getter
     *
     * @return string
     */
    public function getValueElementType()
    {
        return 'text';
    }

    /**
     * Chooser URL getter
     *
     * @return string
     */
    public function getValueElementChooserUrl()
    {
        return $this->_adminhtmlData->getUrl(
            'segment/rule/grid',
            ['value_element_id' => $this->_valueElement->getId(), 'form' => $this->getJsFormObject()]
        );
    }

    /**
     * Enable chooser selection button
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getExplicitApply()
    {
        return true;
    }

    /**
     * Render element HTML
     *
     * @return string
     */
    public function asHtml()
    {
        $this->_valueElement = $this->getValueElement();
        return $this->getTypeElementHtml() . __(
                'If (Segments Reference Implementation) %1 %2',
                $this->getOperatorElementHtml(),
                $this->_valueElement->getHtml()
            ) .
            $this->getRemoveLinkHtml() .
            '<div class="rule-chooser" url="' .
            $this->getValueElementChooserUrl() .
            '"></div>';
    }

    /**
     * @return Condition
     */
    public function loadOperatorOptions()
    {
        parent::loadOperatorOptions();
        $this->setOperatorOption(
            [
                '==' => __('matches'),
                '!=' => __('does not match'),
                '()' => __('is one of'),
                '!()' => __('is not one of'),
            ]
        );
        return $this;
    }

    /**
     * Present selected values as array
     *
     * @return array
     */
    public function getValueParsed()
    {
        $value = $this->getData('value');
        $value = array_map('trim', explode(',', $value));
        return $value;
    }

    /**
     * Validate if qoute customer is assigned to role segments
     *
     * @param   \Magento\Quote\Model\Quote\Address|\Magento\Framework\Model\AbstractModel $object
     * @return  bool
     */
    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {
        $segments = $this->segmentResolver->getSegments();
        return $this->validateAttribute($segments);
    }

    /**
     * Whether this condition can be filtered using index table
     *
     * @return bool
     */
    public function isFilterable()
    {
        return true;
    }

    /**
     * Return a list of filter groups that represent this condition
     *
     * @return FilterGroupInterface[]
     */
    public function getFilterGroups()
    {
        return $this->generator->getFilterGroups($this->getOperator(), $this->getValue());
    }
}
