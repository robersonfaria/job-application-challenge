<?php

namespace App\Services\ParseFile;

use App\Services\ParseFile\Adapters\ParseCsv;
use App\Services\ParseFile\Adapters\ParseJson;
use App\Services\ParseFile\Adapters\ParseXml;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\LazyCollection;

class ParseFile
{

    private string $filename;

    private ParseFileContract $adapter;

    /**
     * @param string $filename
     * @throws Exception
     */
    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }

    /**
     * @return LazyCollection
     * @throws Exception
     */
    public function parse(): LazyCollection
    {
        if (Storage::exists($this->filename) === false) {
            throw new Exception("File " . Storage::path($this->filename) . " not found!");
        }

        return $this->getAdapter()
            ->parse($this->filename);
    }

    /**
     * @param ParseFileContract|null $adapter
     * @return $this
     */
    public function setAdapter(ParseFileContract $adapter = null): self
    {
        $this->adapter = $adapter;
        return $this;
    }

    /**
     * @return ParseFileContract
     * @throws Exception
     */
    protected function getAdapter(): ParseFileContract
    {
        if (empty($this->adapter)) {
            $this->adapter = match (File::extension($this->filename)) {
                'json' => new ParseJson(),
                'csv' => new ParseCsv(),
                'xml' => new ParseXml(),
                default => throw new Exception("Type " . File::extension($this->filename) . " not supported."),
            };
        }
        return $this->adapter;
    }
}
