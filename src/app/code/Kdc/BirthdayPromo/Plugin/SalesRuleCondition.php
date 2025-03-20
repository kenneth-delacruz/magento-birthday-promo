<?php



namespace Kdc\BirthdayPromo\Plugin;

use Magento\SalesRule\Model\Rule\Condition\Combine;
use Kdc\BirthdayPromo\Model\Rule\Condition\BirthdayCondition;

class SalesRuleCondition
{
    /**
     * Adds the Customer Birthday condition to the rule condition options.
     *
     * This method modifies the list of available conditions by appending 
     * the custom birthday condition, allowing it to be selected in rule conditions.
     *
     * @param \Magento\Rule\Model\Condition\Combine $subject The original condition combine model.
     * @param array $result The existing list of selectable condition options.
     * @return array The modified list including the Customer Birthday condition.
     */
    public function afterGetNewChildSelectOptions(Combine $subject, $result)
    {
        $result[] = [
            'value' => BirthdayCondition::class,
            'label' => __('Customer Birthday')
        ];
        return $result;
    }
}
