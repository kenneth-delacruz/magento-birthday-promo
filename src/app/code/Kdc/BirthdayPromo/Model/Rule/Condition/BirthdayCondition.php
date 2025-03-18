<?php
namespace Kdc\BirthdayPromo\Model\Rule\Condition;

use Magento\Rule\Model\Condition\AbstractCondition;
use Magento\Framework\Model\AbstractModel;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Rule\Model\Condition\Context;
use Psr\Log\LoggerInterface;

class BirthdayCondition extends AbstractCondition
{
    protected $customerSession;

    public function __construct(
        Context $context,
        private LoggerInterface $logger,
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
     * Define condition label
     */
    public function loadAttributeOptions()
    {
        $attributes = ['birthday_promo' => __('Customer Birthday')];
        $this->setAttributeOption($attributes);
        return $this;
    }

    /**
     * Validate the condition
     */
    public function validate(AbstractModel $model)
    {
        // Ensure the condition is set before applying logic
        if ($this->getAttribute() !== 'birthday_promo') {
            return false;
        }
    
        $customer = $this->customerSession->getCustomer();
        if (!$customer || !$customer->getId()) {
            return false; // Ensure a customer is logged in
        }
    
        $dob = $customer->getDob(); // Get customer's Date of Birth
        if (!$dob) {
            return false; // No birthday recorded, condition fails
        }
    
        $dobDate = new \DateTime($dob);
        $currentDate = new \DateTime();
    
        // Check if today is the birthday
        return ($dobDate->format('m-d') === $currentDate->format('m-d'));
    }
    
    
}
