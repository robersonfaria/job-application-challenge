<?php

namespace Tests\Unit;

use App\Services\ParseFile\Adapters\ParseXml;
use App\Services\ParseFile\ParseFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ParseFileTest extends TestCase
{

    public function test_parse_json_file()
    {
        $parser = new ParseFile('test.json');
        $parser->generateFile(1);

        $this->assertEquals(1, $parser->parse()->count());

        Storage::delete('test.json');
    }

    public function test_parse_csv_file()
    {
        $parser = new ParseFile('test.csv');
        $parser->generateFile(1);

        $this->assertEquals(1, $parser->parse()->count());

        Storage::delete('test.csv');
    }

    public function test_parse_xml_file()
    {
        $parser = new ParseFile('test.xml');
        $parser->generateFile(1);

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

    public function test_parse_with_use_adapater()
    {
        $parser = new ParseFile('test.xml');
        $parser->setAdapter(new ParseXml());
        $parser->generateFile(3);

        $this->assertEquals(3, $parser->parse()->count());

        Storage::delete('test.xml');
    }
}
