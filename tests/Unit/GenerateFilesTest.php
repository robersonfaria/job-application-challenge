<?php

namespace Tests\Unit;

use App\Services\Utils\GenerateFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GenerateFilesTest extends TestCase
{

    public function test_generate_json_file()
    {
        $generator = (new GenerateFile('json', 'test', 1));
        $generator->run();

        $filePath = Storage::path('test.json');
        $this->assertFileExists($filePath);

        Storage::delete($filePath);
    }

    public function test_generate_csv_file()
    {
        $generator = (new GenerateFile('csv', 'test', 1));
        $generator->run();

        $filePath = Storage::path('test.csv');
        $this->assertFileExists($filePath);

        Storage::delete($filePath);
    }

    public function test_generate_xml_file()
    {
        $generator = (new GenerateFile('xml', 'test', 1));
        $generator->run();

        $filePath = Storage::path('test.xml');
        $this->assertFileExists($filePath);

        Storage::delete($filePath);
    }

    public function test_generate_biggest_file()
    {
        $generator = (new GenerateFile('xml', 'test', 1001));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('This function is not prepared to create biggest files');

        $generator->run();
    }

    public function test_generate_file_invalid_format()
    {
        $generator = (new GenerateFile('jpg', 'test', 1001));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Type jpg not supported.");

        $generator->run();
    }
}
