<?php

namespace Tests\Feature;

use App\Models\Card;
use App\Models\Customer;
use App\Services\ParseFile\Adapters\ParseJson;
use App\Services\ParseFile\ParseFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProcessFileTest extends TestCase
{

    use RefreshDatabase;

    public function test_process_json_file()
    {
        $parse = new ParseFile('test.json');
        $parse->generateFile(3);

        $this->artisan('challenge:process-file -f test.json')->assertExitCode(0);

        $this->assertDatabaseCount(Customer::class, 3);

        Storage::delete('test.json');
    }

    public function test_process_json_file_with_3_digits()
    {
        $parser = new ParseFile('test.json');
        $adapter = (new ParseJson())
            ->setCard(
                function () {
                    return Card::factory()
                        ->state([
                            'number' => '1234555067891234'
                        ])
                        ->make();
                });
        $parser->setAdapter($adapter)
            ->generateFile(3);

        $this->artisan('challenge:process-file -f test.json --3digits')->assertExitCode(0);

        $this->assertDatabaseCount(Customer::class, 3);

        Storage::delete('test.json');
    }

    public function test_process_json_file_with_invalid_age()
    {
        $parser = new ParseFile('test.json');
        $adapter = (new ParseJson())
            ->setCustomer(
                function () {
                    return Customer::factory()
                        ->state([
                            'date_of_birth' => now()->subYear(array_rand([17, 66]))->toString()
                        ])
                        ->make();
                });
        $parser->setAdapter($adapter)
            ->generateFile(3);

        $this->artisan('challenge:process-file -f test.json')->assertExitCode(0);

        $this->assertDatabaseCount(Customer::class, 0);

        Storage::delete('test.json');
    }
}
