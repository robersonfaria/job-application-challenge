<?php

namespace App\Services\ParseFile\Adapters;

use App\Services\ParseFile\ParseFileContract;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\LazyCollection;

class ParseCsv implements ParseFileContract
{

    public function parse(string $filename): LazyCollection
    {
        $stream = Storage::readStream($filename);
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
}
