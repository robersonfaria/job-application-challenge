<?php

namespace App\Services\Utils;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\LazyCollection;
use pcrov\JsonReader\JsonReader;

class ParseFile
{

    /**
     * @var string
     */
    private $filename;

    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }

    /**
     * @return LazyCollection
     * @throws \Exception
     */
    public function parse(): LazyCollection
    {
        if (Storage::exists($this->filename) === false) {
            throw new \Exception("File " . Storage::path($this->filename) . " not found!");
        }

        if (method_exists($this, File::extension($this->filename)) === false) {
            throw new \Exception("Type " . File::extension($this->filename) . " not supported.");
        }

        return $this->{File::extension($this->filename)}();
    }

    /**
     * @return LazyCollection
     */
    private function json(): LazyCollection
    {
        $stream = Storage::readStream($this->filename);
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
     * @return LazyCollection
     */
    private function csv(): LazyCollection
    {
        $stream = Storage::readStream($this->filename);
        return LazyCollection::make(function () use ($stream) {
            $keys = fgetcsv($stream, null, ';');
            while (($line = fgetcsv($stream, null, ';')) !== false) {
                yield array_combine($keys, $line);
            }
        })->map(function ($data) {
            $data["credit_card"] = [
                'account' => $data["account"],
                'type' => $data["type"],
                'number' => $data["number"],
                'expirationDate' => $data["expirationDate"],
            ];
            $data['checked'] = (boolean)$data['checked'];
            unset(
                $data['account'],
                $data['type'],
                $data['number'],
                $data['expirationDate'],
            );
            return $data;
        });
    }

    private function xml(): LazyCollection
    {
        $xml = new \XMLReader();
        $xml->open(Storage::path($this->filename));
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
