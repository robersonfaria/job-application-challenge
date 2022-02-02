<?php

namespace Tests\Feature;

use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GenerateFileCommandTest extends TestCase
{

    use RefreshDatabase;

    public function test_command()
    {
        $this->artisan('challenge:generate-file -r 3 test.json')->assertExitCode(0);

        $this->assertFileExists(Storage::path('test.json'));

        Storage::delete('test.json');
    }

    public function test_invalid_extension_command()
    {
        $this->artisan('challenge:generate-file -r 3 test.doc')->assertExitCode(0);

        $this->assertFileDoesNotExist(Storage::path('test.doc'));
    }
}
