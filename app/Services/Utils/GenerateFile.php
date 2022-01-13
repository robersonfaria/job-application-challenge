<?php

namespace App\Services\Utils;

use Illuminate\Support\Facades\Storage;
use Spatie\ArrayToXml\ArrayToXml;

class GenerateFile
{

    use DateTrait;

    /**
     * @var string
     */
    private $filename;
    /**
     * @var int
     */
    private $records;
    /**
     * @var string
     */
    private $type;

    private $resource;

    public function __construct(string $type, $filename = 'challenge', $records = 10)
    {
        $this->type = $type;
        $this->filename = $filename . '.' . $type;
        $this->records = $records;
    }

    public function run()
    {
        if (method_exists($this, $this->type) === false) {
            throw new \Exception("Type {$this->type} not supported.");
        }

        if ($this->records > 1000) {
            throw new \Exception('This function is not prepared to create biggest files');
        }

        $this->{$this->type}();
    }

    private function json()
    {
        $data = [];
        for ($i = 0; $i < $this->records; $i++) {
            $customer = $this->getCustomer();
            $customer['credit_card'] = $this->getCard();
            $data[] = $customer;
        }
        Storage::put($this->filename, json_encode($data));
    }

    private function csv()
    {
        for ($i = 0; $i < $this->records; $i++) {
            $customer = $this->getCustomer();
            $customer += $this->getCard();
            if ($i === 0) {
                Storage::put($this->filename, implode(';', array_keys($customer)));
            }
            Storage::append($this->filename, implode(';', $customer));
        }
    }

    private function xml()
    {
        $data = [];
        for ($i = 0; $i < $this->records; $i++) {
            $customer = $this->getCustomer();
            $customer['credit_card'] = $this->getCard();
            $data[] = $customer;
        }
        $xml = new ArrayToXml(['row' => $data], [], true, 'UTF-8', '1.0', [], true);
        Storage::put($this->filename, $xml->toXml());
    }
}
