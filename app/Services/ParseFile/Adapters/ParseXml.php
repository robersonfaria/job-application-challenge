<?php

namespace App\Services\ParseFile\Adapters;

use App\Services\ParseFile\ParseFileContract;
use DOMDocument;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\LazyCollection;
use Spatie\ArrayToXml\ArrayToXml;
use XMLReader;

class ParseXml implements ParseFileContract
{
    use GenerateFakeData;

    /**
     * @param string $filename
     * @return LazyCollection
     */
    public function parse(string $filename): LazyCollection
    {
        $xml = new XMLReader();
        $xml->open(Storage::path($filename));
        return LazyCollection::make(function () use ($xml) {
            $doc = new DOMDocument();
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

    /**
     * @param string $filename
     * @param int $records
     * @return bool
     */
    public function generate(string $filename, int $records = 10): bool
    {
        $data = [];
        for ($i = 0; $i < $records; $i++) {
            $customer = $this->getCustomer()->toArray();
            $customer['credit_card'] = $this->getCard()->toArray();
            $data[] = $customer;
        }
        $xml = new ArrayToXml(['row' => $data], [], true, 'UTF-8', '1.0', [], true);
        Storage::put($filename, $xml->toXml());
        return true;
    }
}
