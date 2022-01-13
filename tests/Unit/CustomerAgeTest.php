<?php

namespace Tests\Unit;

use App\Exceptions\AgeRangeNotAllowedException;
use App\Models\Customer;
use App\Services\CustomerService;
use Tests\TestCase;

class CustomerAgeTest extends TestCase
{

    public function test_get_accessor_age()
    {
        $yearsSub = rand(0,100);
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

        $this->assertFalse($customer->age);
    }

    public function test_validate_age_range()
    {
        $customer = Customer::factory()->state([
            'date_of_birth' => now()->subYear(100)->toString()
        ])->make();

        $this->expectException(AgeRangeNotAllowedException::class);

        CustomerService::validateAgeRange($customer);
    }

    public function test_invalid_format_date()
    {
        $customer = Customer::factory()->state([
            'date_of_birth' => now()->format('d/m/Y')
        ])->make();

        $this->assertEquals(now()->startOfDay(), $customer->date_of_birth);
    }
}
