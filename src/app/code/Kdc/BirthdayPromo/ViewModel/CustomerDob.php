<?php

namespace Kdc\BirthdayPromo\ViewModel;

use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory as RuleCollectionFactory;

class CustomerDob implements ArgumentInterface
{
    protected $customerSession;
    protected $ruleCollectionFactory;

    public function __construct(
        Session $customerSession,
        RuleCollectionFactory $ruleCollectionFactory
    ) {
        $this->customerSession = $customerSession;
        $this->ruleCollectionFactory = $ruleCollectionFactory;
    }

    public function getCustomerDob()
    {
        if ($this->customerSession->isLoggedIn()) {
            $dob = $this->customerSession->getCustomer()->getDob();
            
            if ($dob) {
                $dobDate = new \DateTime($dob);
                $today = new \DateTime();

                if ($dobDate->format('m-d') === $today->format('m-d')) {
                    $discountDetails = $this->getBirthdayDiscountDetails();
                    $discountText = $this->formatDiscountMessage($discountDetails);
                    return $discountText;
                }
            }
        }
        return null; // Return null if it's not their birthday
    }

    private function getBirthdayDiscountDetails()
    {
        $ruleCollection = $this->ruleCollectionFactory->create();
        $ruleCollection->addFieldToFilter('name', 'Birthday 50% Off')->setPageSize(1);

        $discountAmount = 0; // Default
        $simpleAction = 'by_percent'; // Default action type

        if ($ruleCollection->getSize() > 0) {
            $rule = $ruleCollection->getFirstItem();
            $discountAmount = (float) $rule->getDiscountAmount();
            $simpleAction = $rule->getSimpleAction();
        }

        return [
            'amount' => $discountAmount,
            'action' => $simpleAction
        ];
    }

    private function formatDiscountMessage($discountDetails)
    {
        switch ($discountDetails['action']) {
            case 'by_percent':
                return "{$discountDetails['amount']}%";
            case 'by_fixed':
                return "{$discountDetails['amount']} off";
            case 'cart_fixed':
                return "{$discountDetails['amount']} off your cart total";
            case 'buy_x_get_y':
                return "a Buy X Get Y offer";
            default:
                return "{$discountDetails['amount']} discount";
        }
    }
}
