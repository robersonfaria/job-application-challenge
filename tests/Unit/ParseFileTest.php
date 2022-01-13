<?php

namespace Tests\Unit;

use App\Services\Utils\GenerateFile;
use App\Services\Utils\ParseFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ParseFileTest extends TestCase
{

    public function test_parse_json_file()
    {
        $generator = new GenerateFile('json', 'test', 1);
        $generator->run();

        $parser = new ParseFile('test.json');
        $this->assertEquals(1, $parser->parse()->count());

        Storage::delete('test.json');
    }

    public function test_parse_csv_file()
    {
        $generator = new GenerateFile('csv', 'test', 1);
        $generator->run();

        $parser = new ParseFile('test.csv');
        $this->assertEquals(1, $parser->parse()->count());

        Storage::delete('test.csv');
    }

    public function test_parse_xml_file()
    {
        $generator = new GenerateFile('xml', 'test', 1);
        $generator->run();

        $parser = new ParseFile('test.xml');
        $this->assertEquals(1, $parser->parse()->count());

        Storage::delete('test.xml');
    }

    public function test_parse_file_not_found()
    {
        $parser = new ParseFile('test.json');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("File /var/www/html/storage/app/test.json not found!");

        $parser->parse();
    }

    public function test_parse_type_not_supported()
    {
        Storage::put('test.txt', 'test');
        $parser = new ParseFile('test.txt');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Type txt not supported.");

        $parser->parse();

        Storage::delete('test.txt');
    }
}
