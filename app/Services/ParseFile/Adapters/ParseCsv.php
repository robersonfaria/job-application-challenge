<?php

namespace App\Services\ParseFile\Adapters;

use App\Services\ParseFile\ParseFileContract;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\LazyCollection;

class ParseCsv implements ParseFileContract
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

    /**
     * @param string $filename
     * @param int $records
     * @return bool
     */
    public function generate(string $filename, int $records = 10): bool
    {
        for ($i = 0; $i < $records; $i++) {
            $customer = $this->getCustomer()->toArray();
            $customer += $this->getCard()->toArray();
            if ($i === 0) {
                Storage::put($filename, implode(';', array_keys($customer)));
            }
            Storage::append($filename, implode(';', $customer));
        }
        return true;
    }
}
