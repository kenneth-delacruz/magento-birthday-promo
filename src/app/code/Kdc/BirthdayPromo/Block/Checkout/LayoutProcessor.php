<?php

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

    public function __construct(
        CustomerDob $customerDob,
        CustomerSession $customerSession,
        BirthdayMessageHelper $birthdayMessageHelper,
    ) {
        $this->customerDob = $customerDob;
        $this->customerSession = $customerSession;
        $this->birthdayMessageHelper = $birthdayMessageHelper;
    }

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
