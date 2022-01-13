<?php

namespace App\Services\Utils;

use App\Models\Card;
use App\Models\Customer;

trait DateTrait
{

    /**
     * @var \Closure
     */
    private $customer;

    /**
     * @var \Closure
     */
    private $card;

    public function setCustomer(\Closure $callback)
    {
        $this->customer = $callback;
        return $this;
    }

    public function getCustomer()
    {
        if (empty($this->customer)) {
            return Customer::factory()->acceptedAge()->make()->toArray();
        }
        return ($this->customer)();
    }

    public function setCard(\Closure $callback)
    {
        $this->card = $callback;
        return $this;
    }

    public function getCard()
    {
        if (empty($this->card)) {
            return Card::factory()->make()->toArray();
        }
        return ($this->card)();
    }
}
