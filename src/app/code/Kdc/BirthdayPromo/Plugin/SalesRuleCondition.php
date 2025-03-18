<?php
namespace Kdc\BirthdayPromo\Plugin;

use Magento\SalesRule\Model\Rule\Condition\Combine;
use Kdc\BirthdayPromo\Model\Rule\Condition\BirthdayCondition;

class SalesRuleCondition
{
    public function afterGetNewChildSelectOptions(Combine $subject, $result)
    {
        $result[] = [
            'value' => BirthdayCondition::class,
            'label' => __('Customer Birthday')
        ];
        return $result;
    }
}
