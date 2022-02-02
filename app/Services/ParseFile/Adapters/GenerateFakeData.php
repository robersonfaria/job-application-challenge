<?php

namespace App\Services\ParseFile\Adapters;

use App\Models\Card;
use App\Models\Customer;
use App\Services\ParseFile\ParseFileContract;
use Closure;

trait GenerateFakeData
{

    /**
     * @var Closure
     */
    private Closure $customer;

    /**
     * @var Closure
     */
    private Closure $card;

    /**
     * @param Closure $callback
     * @return ParseFileContract
     */
    public function setCustomer(Closure $callback): ParseFileContract
    {
        $this->customer = $callback;
        return $this;
    }

    /**
     * @return Customer
     */
    public function getCustomer(): Customer
    {
        if (empty($this->customer)) {
            return Customer::factory()->acceptedAge()->make();
        }
        return ($this->customer)();
    }

    /**
     * @param Closure $callback
     * @return ParseFileContract
     */
    public function setCard(Closure $callback): ParseFileContract
    {
        $this->card = $callback;
        return $this;
    }


    /**
     * @return Card
     */
    public function getCard(): Card
    {
        if (empty($this->card)) {
            return Card::factory()->make();
        }
        return ($this->card)();
    }
}
