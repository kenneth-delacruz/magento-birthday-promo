<?php

namespace Kdc\BirthdayPromo\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class BirthdayMessage extends AbstractHelper
{
    public function getBirthdayMessage($dobDiscount)
    {
        return __('🎉 Happy Birthday! 🎊 <br> 🎈 Enjoy a fantastic <b>%1</b> discount! 🥳', $dobDiscount);
    }
}
