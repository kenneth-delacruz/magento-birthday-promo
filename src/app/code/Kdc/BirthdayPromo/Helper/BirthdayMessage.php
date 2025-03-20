<?php

declare(strict_types=1);

namespace Kdc\BirthdayPromo\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class BirthdayMessage extends AbstractHelper
{
    /**
     * Generates a birthday discount message.
     *
     * This method returns a formatted birthday message including the discount amount.
     *
     * @param mixed $dobDiscount The discount value associated with the customer's birthday.
     * @return \Magento\Framework\Phrase A formatted birthday message with the discount.
     */
    public function getBirthdayMessage($dobDiscount)
    {
        return __('🎉 Happy Birthday! 🎊 <br> 🎈 Enjoy a fantastic <b>%1</b> discount! 🥳', $dobDiscount);
    }
}
