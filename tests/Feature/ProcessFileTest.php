<?php

namespace Tests\Feature;

use App\Exceptions\AgeRangeNotAllowedException;
use App\Models\Card;
use App\Models\Customer;
use App\Services\Utils\GenerateFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProcessFileTest extends TestCase
{

    use RefreshDatabase;

    public function test_process_json_file()
    {
        $generator = (new GenerateFile('json', 'test', 3));
        $generator->run();

        $this->artisan('challenge:process-file -f test.json')->assertExitCode(0);

        $this->assertDatabaseCount(Customer::class, 3);

        Storage::delete('test.json');
    }

    public function test_process_json_file_with_3_digits()
    {
        $generator = (new GenerateFile('json', 'test', 3));
        $generator->setCard(function () {
            return Card::factory()->state([
                'number' => '1234555067891234'
            ])->make();
        });
        $generator->run();

        $this->artisan('challenge:process-file -f test.json --3digits')->assertExitCode(0);

        $this->assertDatabaseCount(Customer::class, 3);

        Storage::delete('test.json');
    }

    public function test_process_json_file_with_invalid_age()
    {
        $generator = (new GenerateFile('json', 'test', 3));
        $generator->setCustomer(function () {
            return Customer::factory()->state([
                'date_of_birth' => now()->subYear(array_rand([17,66]))->toString()
            ])->make();
        });
        $generator->run();

        $this->artisan('challenge:process-file -f test.json')->assertExitCode(0);

        $this->assertDatabaseCount(Customer::class, 0);

        Storage::delete('test.json');
    }
}
