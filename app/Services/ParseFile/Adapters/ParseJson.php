<?php

namespace App\Services\ParseFile\Adapters;

use App\Services\ParseFile\ParseFileContract;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\LazyCollection;
use pcrov\JsonReader\JsonReader;

class ParseJson implements ParseFileContract
{
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


}
