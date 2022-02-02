<?php

namespace App\Services;

use App\Exceptions\AgeRangeNotAllowedException;

class CustomerService
{

    /**
     * @param $customer
     * @return void
     * @throws AgeRangeNotAllowedException
     */
    public static function validateAgeRange($customer)
    {
        if (($customer->age >= 18 && $customer->age <= 65) === false) {
            throw new AgeRangeNotAllowedException('Customer outside the allowed age range');
        }
    }
}
