<?php

namespace Tests\Unit;

use App\Exceptions\CardWithout3OrMoreConsecutiveSameDigitsException;
use App\Models\Card;
use App\Services\CardService;
use Tests\TestCase;

class CardNumberTest extends TestCase
{

    public function test_card_without_3_consecutive_same_digits()
    {
        $card = Card::factory()->state([
            'number' => '1234567891234567'
        ])->make();

        $this->expectException(CardWithout3OrMoreConsecutiveSameDigitsException::class);

        CardService::validateExistsConsecutiveSameDigits($card);
    }
}
