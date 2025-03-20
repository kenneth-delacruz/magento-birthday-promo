<?php

declare(strict_types=1);

namespace Kdc\BirthdayPromo\Block\Checkout;

use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Kdc\BirthdayPromo\ViewModel\CustomerDob;
use Magento\Customer\Model\Session as CustomerSession;
use Kdc\BirthdayPromo\Helper\BirthdayMessage as BirthdayMessageHelper;

class LayoutProcessor implements LayoutProcessorInterface
{
    protected $customerDob;
    protected $customerSession;
    protected $birthdayMessageHelper;
    protected $logger;

    /**
     * Constructor for injecting dependencies.
     *
     * Initializes the required dependencies for retrieving customer's discount based on date of birth if they acquire birthday discount,
     * managing customer session, and generating birthday messages.
     *
     * @param \Kdc\BirthdayPromo\ViewModel\CustomerDob $customerDob Handles retrieval of the customer's discount based on date of birth if they acquire birthday discount.
     * @param \Magento\Customer\Model\Session $customerSession Manages customer session data.
     * @param \Kdc\BirthdayPromo\Helper\BirthdayMessage $birthdayMessageHelper Generates birthday-related messages.
     */
    public function __construct(
        CustomerDob $customerDob,
        CustomerSession $customerSession,
        BirthdayMessageHelper $birthdayMessageHelper,
    ) {
        $this->customerDob = $customerDob;
        $this->customerSession = $customerSession;
        $this->birthdayMessageHelper = $birthdayMessageHelper;
    }

    /**
     * Processes the checkout jsLayout to include the birthday discount message.
     * 
     * If the customer is logged in and has a date of birth of today they get birthday discount, it retrieves the birthday
     * message and adds a custom birthday discount component to the checkout summary.
     * 
     * @param mixed $jsLayout The existing checkout JS layout configuration.
     * @return array The modified JS layout with the birthday discount component if applicable.
     */
    public function process($jsLayout)
    {
        if ($this->customerSession->isLoggedIn()) {
            $dobDiscount = $this->customerDob->getCustomerDob();

            if ($dobDiscount) {
                $dobMessage = $this->birthdayMessageHelper->getBirthdayMessage($dobDiscount);

                $jsLayout['components']['checkout']['children']['sidebar']['children']['summary']['children']['itemsBefore']['children']['birthday_discount'] = [
                    'component' => 'Kdc_BirthdayPromo/js/view/summary/birthday-discount',
                    'config' => [
                        'template' => 'Kdc_BirthdayPromo/checkout/summary/birthday-discount',
                        'dobMessage' => $dobMessage,
                    ]
                ];
            }
        }

        return $jsLayout;
    }
}
