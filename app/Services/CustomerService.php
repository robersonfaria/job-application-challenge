<?php

namespace App\Services;

use App\Exceptions\AgeRangeNotAllowedException;

abstract class CustomerService
{

    public static function validateAgeRange($customer)
    {
        if ($customer->age < 18 || $customer->age > 65) {
            throw new AgeRangeNotAllowedException('Customer outside the allowed age range');
        }
    }
}
