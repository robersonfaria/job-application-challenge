<?php

namespace App\Services\ParseFile\Adapters;

use App\Services\ParseFile\ParseFileContract;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\LazyCollection;

class ParseXml implements ParseFileContract
{

    public function parse(string $filename): LazyCollection
    {
        $xml = new \XMLReader();
        $xml->open(Storage::path($filename));
        return LazyCollection::make(function () use ($xml) {
            $doc = new \DOMDocument();
            while ($xml->read() && $xml->name !== 'row') ;

            while ($xml->name === 'row') {
                $node = simplexml_import_dom($doc->importNode($xml->expand(), true));
                yield $node;
                $xml->next('row');
            }
        })->map(function ($data) {
            $data = (array)json_decode(json_encode($data));
            $data['checked'] = (boolean)$data['checked'];
            $data['credit_card'] = (array)$data['credit_card'];
            return $data;
        });
    }
}
