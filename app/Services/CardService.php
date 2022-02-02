<?php

namespace App\Services;

use App\Exceptions\CardWithout3OrMoreConsecutiveSameDigitsException;

class CardService
{

    public static function validateExistsConsecutiveSameDigits($card)
    {
        if (hasConsecutiveSameDigits($card) === false) {
            throw new CardWithout3OrMoreConsecutiveSameDigitsException('The card informed does not have 3 or more consecutive digits that are the same');
        }
    }
}
