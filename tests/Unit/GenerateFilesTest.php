<?php

namespace Tests\Unit;


use App\Models\Customer;
use App\Services\ParseFile\Adapters\ParseJson;
use App\Services\ParseFile\ParseFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GenerateFilesTest extends TestCase
{

    public function test_generate_json_file()
    {
        $generator = new ParseFile('test.json');
        $generator->generateFile(1);

        $filePath = Storage::path('test.json');
        $this->assertFileExists($filePath);

        Storage::delete($filePath);
    }

    public function test_generate_csv_file()
    {
        $generator = new ParseFile('test.csv');
        $generator->generateFile(1);

        $filePath = Storage::path('test.csv');
        $this->assertFileExists($filePath);

        Storage::delete($filePath);
    }

    public function test_generate_xml_file()
    {
        $generator = new ParseFile('test.xml');
        $generator->generateFile(1);

        $filePath = Storage::path('test.xml');
        $this->assertFileExists($filePath);

        Storage::delete($filePath);
    }

    public function test_generate_biggest_file()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('This function is not prepared to create biggest files');

        $generator = new ParseFile('test.xml');
        $generator->generateFile(1001);
    }

    public function test_generate_file_invalid_format()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("This type of file(Jpg) does not have an implementation for generating files");

        $generator = new ParseFile('test.jpg');
        $generator->generateFile(1);
    }

    public function test_generate_file_invalid_data_format()
    {
        $this->expectException(\TypeError::class);
        $generator = new ParseFile('test.json');
        $adapter = (new ParseJson())
            ->setCustomer(
                fn() => [
                    'date_of_birth' => true
                ]
            );
        $generator->setAdapter($adapter)->generateFile(1);
    }
}
