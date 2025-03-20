<?php

declare(strict_types=1);

namespace Kdc\BirthdayPromo\ViewModel;

use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory as RuleCollectionFactory;
use DateTime;

class CustomerDob implements ArgumentInterface
{
    protected $customerSession;
    protected $ruleCollectionFactory;

    /**
     * Constructor for initializing customer session and rule collection factory.
     *
     * Injects dependencies required for retrieving customer session data and fetching 
     * applicable sales rules.
     *
     * @param \Magento\Customer\Model\Session $customerSession Manages the customer session data.
     * @param \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory $ruleCollectionFactory Factory for creating sales rule collections.
     */
    public function __construct(
        Session $customerSession,
        RuleCollectionFactory $ruleCollectionFactory
    ) {
        $this->customerSession = $customerSession;
        $this->ruleCollectionFactory = $ruleCollectionFactory;
    }

    /**
     * Checks if today is the customer's birthday and returns the discount message.
     *
     * This method verifies if the logged-in customer's date of birth matches today's date.
     * If it does, it retrieves the birthday discount details and formats a discount message.
     *
     * @return string|null The formatted birthday discount message if applicable, otherwise null.
     */
    public function getCustomerDob()
    {
        if ($this->customerSession->isLoggedIn()) {
            $dob = $this->customerSession->getCustomer()->getDob();
            
            if ($dob) {
                $dobDate = new DateTime($dob);
                $today = new DateTime();

                if ($dobDate->format('m-d') === $today->format('m-d')) {
                    $discountDetails = $this->getBirthdayDiscountDetails();
                    $discountText = $this->formatDiscountMessage($discountDetails);
                    return $discountText;
                }
            }
        }
        return null;
    }

    /**
     * Retrieves the details of the birthday discount rule.
     *
     * This method fetches the sales rule named "Birthday 50% Off" to determine the discount amount 
     * and discount type. If the rule is not found, default values (50% off by percentage) are used.
     *
     * @return array An associative array containing:
     *               - 'amount' (float): The discount amount.
     *               - 'action' (string): The discount type (e.g., 'by_percent').
     */
    private function getBirthdayDiscountDetails()
    {
        $ruleCollection = $this->ruleCollectionFactory->create();
        $ruleCollection->addFieldToFilter('name', 'Birthday 50% Off')->setPageSize(1);

        $discountAmount = 50; // Default
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

    /**
     * Formats the discount message based on the discount type.
     *
     * This method converts the discount details into a readable message depending on 
     * the discount action type (e.g., percentage discount, fixed discount, cart total discount, etc.).
     *
     * @param array $discountDetails An associative array containing:
     *                               - 'amount' (float): The discount amount.
     *                               - 'action' (string): The discount type (e.g., 'by_percent', 'by_fixed').
     * @return string A formatted discount message describing the applicable discount.
     */
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
