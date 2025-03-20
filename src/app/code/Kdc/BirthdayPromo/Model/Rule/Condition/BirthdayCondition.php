<?php

declare(strict_types=1);

namespace Kdc\BirthdayPromo\Model\Rule\Condition;

use Magento\Rule\Model\Condition\AbstractCondition;
use Magento\Framework\Model\AbstractModel;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Rule\Model\Condition\Context;
use DateTime;

class BirthdayCondition extends AbstractCondition
{
    protected $customerSession;

    /**
     * Constructor for initializing the condition model.
     *
     * Injects dependencies required for handling customer session data and rule conditions.
     *
     * @param \Magento\Rule\Model\Condition\Context $context Provides context for rule conditions.
     * @param \Magento\Customer\Model\Session $customerSession Manages the customer session data.
     * @param array $data Additional data passed to the parent constructor.
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        array $data = []
    ) {
        $this->customerSession = $customerSession;
        parent::__construct($context, $data);
    }

    /**
     * Define input type
     */
    public function getInputType()
    {
        return 'numeric';
    }

    /**
     * Define UI element type
     */
    public function getValueElementType()
    {
        return 'text';
    }

    /**
     * Defines the condition label for the birthday promotion.
     *
     * Adds a custom condition attribute option for customer birthday, allowing it
     * to be used in rule conditions.
     *
     * @return static
     */
    public function loadAttributeOptions()
    {
        $attributes = ['birthday_promo' => __('Customer Birthday')];
        $this->setAttributeOption($attributes);
        return $this;
    }

    /**
     * Validates the birthday condition for the promotion.
     *
     * This method checks if the logged-in customer's birthday matches the current date.
     * If the customer is not logged in or does not have a recorded date of birth, 
     * the condition will fail.
     *
     * @param \Magento\Framework\Model\AbstractModel $model The model being validated (not directly used).
     * @return bool True if today is the customer's birthday, otherwise false.
     */
    public function validate(AbstractModel $model)
    {
        $customer = $this->customerSession->getCustomer();
        if (!$customer || !$customer->getId()) {
            return false;
        }
    
        $dob = $customer->getDob();
        if (!$dob) {
            return false;
        }
    
        $dobDate = new DateTime($dob);
        $currentDate = new DateTime();
    
        return ($dobDate->format('m-d') === $currentDate->format('m-d'));
    }
    
    
}
