<?php

namespace Tests\Unit;

use App\Exceptions\AgeRangeNotAllowedException;
use App\Models\Customer;
use App\Services\CustomerService;
use Carbon\Carbon;
use Tests\TestCase;

class CustomerAgeTest extends TestCase
{

    public function test_get_accessor_age()
    {
        $yearsSub = rand(0, 100);
        $customer = Customer::factory()->state([
            'date_of_birth' => now()->subYear($yearsSub)->toString()
        ])->make();

        $this->assertEquals($yearsSub, $customer->age);
    }

    public function test_get_age_unknown()
    {
        $customer = Customer::factory()->state([
            'date_of_birth' => null
        ])->make();

        $this->assertEmpty($customer->age);
    }

    public function test_validate_age_range()
    {
        $customer = Customer::factory()->state([
            'date_of_birth' => now()->subYear(array_rand([17, 66]))->toString()
        ])->make();

        $this->expectException(AgeRangeNotAllowedException::class);

        CustomerService::validateAgeRange($customer);
    }

    public function test_invalid_format_date()
    {
        $date = '20/11/2022';
        $customer = Customer::factory()->state([
            'date_of_birth' => $date
        ])->make();

        $this->assertEquals(Carbon::createFromFormat('d/m/Y', $date)->startOfDay(), $customer->date_of_birth);
    }
}
