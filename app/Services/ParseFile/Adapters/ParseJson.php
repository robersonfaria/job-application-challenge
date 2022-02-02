<?php

namespace App\Services\ParseFile\Adapters;

use App\Services\ParseFile\ParseFileContract;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\LazyCollection;
use pcrov\JsonReader\JsonReader;

class ParseJson implements ParseFileContract
{
    use GenerateFakeData;

    /**
     * @param string $filename
     * @return LazyCollection
     */
    public function parse(string $filename): LazyCollection
    {
        $stream = Storage::readStream($filename);
        return LazyCollection::make(function () use ($stream) {
            $reader = new JsonReader();
            $reader->stream($stream);
            $reader->read(); // Begin array
            $reader->read(); // Step to the first element
            $i = 0;
            do {
                $cursor = $reader->value();
                yield ($cursor);
                $i++;
            } while ($reader->next() && $reader->type() === JsonReader::OBJECT);
            $reader->close();
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
            $customer = $this->getCustomer();
            $customer['credit_card'] = $this->getCard();
            $data[] = $customer;
        }
        Storage::put($filename, json_encode($data));
        return true;
    }
}
