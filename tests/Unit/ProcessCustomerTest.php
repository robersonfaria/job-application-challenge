<?php

namespace Tests\Unit;

use App\Exceptions\AgeRangeNotAllowedException;
use App\Jobs\ProcessCustomer;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProcessCustomerTest extends TestCase
{

    use RefreshDatabase, WithFaker;

    public function test_process_valid_data()
    {
        $data = [
            "name" => "Prof. Simeon Green",
            "address" => "328 Bergstrom Heights Suite 709 49592 Lake Allenville",
            "checked" => false,
            "description" => "Voluptatibus nihil dolor quaerat. Reprehenderit est molestias quia nihil consectetur voluptatum et.<br>Ea officiis ex ea suscipit dolorem. Ut ab vero fuga.<br>Quam ipsum nisi debitis repudiandae quibusdam. Sint quisquam vitae rerum nobis.",
            "interest" => null,
            "date_of_birth" => "1989-03-21T01:11:13+00:00",
            "email" => "nerdman@cormier.net",
            "account" => "556436171909",
            "credit_card" => [
                "type" => "Visa",
                "number" => "4532383564703",
                "name" => "Brooks Hudson",
                "expirationDate" => "12\/19"
            ]
        ];
        ProcessCustomer::dispatch($data);

        $this->assertDatabaseHas(Customer::class, ['email' => $data['email']]);
    }

    public function test_process_invalid_age()
    {
        $data = [
            "name" => "Prof. Simeon Green",
            "address" => "328 Bergstrom Heights Suite 709 49592 Lake Allenville",
            "checked" => false,
            "description" => "Voluptatibus nihil dolor quaerat. Reprehenderit est molestias quia nihil consectetur voluptatum et.<br>Ea officiis ex ea suscipit dolorem. Ut ab vero fuga.<br>Quam ipsum nisi debitis repudiandae quibusdam. Sint quisquam vitae rerum nobis.",
            "interest" => null,
            "date_of_birth" => Carbon::now()->subYear($this->faker->randomElement([1, 17, 66, 100]))->toString(),
            "email" => "nerdman@cormier.net",
            "account" => "556436171909",
            "credit_card" => [
                "type" => "Visa",
                "number" => "4532383564703",
                "name" => "Brooks Hudson",
                "expirationDate" => "12\/19"
            ]
        ];
        $this->expectException(AgeRangeNotAllowedException::class);

        ProcessCustomer::dispatch($data);
    }
}
