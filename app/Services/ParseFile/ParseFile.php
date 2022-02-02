<?php

namespace App\Services\ParseFile;

use App\Services\ParseFile\Adapters\ParseCsv;
use App\Services\ParseFile\Adapters\ParseJson;
use App\Services\ParseFile\Adapters\ParseXml;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Str;

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
     * @param int $records
     * @return bool
     * @throws Exception
     */
    public function generateFile(int $records = 10): bool
    {
        if (Storage::exists($this->filename)) {
            Storage::delete($this->filename);
        }

        if($records > 1000) {
            throw new Exception('This function is not prepared to create biggest files');
        }

        /**
         * ATTENTION: I could have called the same getAdapter() method here, but I'm just exemplifying different approaches.
         */
        return $this->getGenerateAdapter()
            ->generate($this->filename, $records);
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
     * Approach 1
     *
     * The getAdapter() and getGenerateAdapter() methods have exactly the same function, but implement different approaches.
     * @return ParseFileContract
     * @throws Exception
     */
    protected function getAdapter(): ParseFileContract
    {
        if (empty($this->adapter)) {
            /**
             * In this method, the adapter is identified by the file extension, but a conditional is checked to find the correct adapter.
             * When implementing a new adapter it is necessary to increment the conditionals to get to know it
             */
            $this->adapter = match (File::extension($this->filename)) {
                'json' => new ParseJson(),
                'csv' => new ParseCsv(),
                'xml' => new ParseXml(),
                default => throw new Exception("Type " . File::extension($this->filename) . " not supported."),
            };
        }
        return $this->adapter;
    }

    /**
     * Approach 2
     *
     * The getAdapter() and getGenerateAdapter() methods have exactly the same function, but implement different approaches.
     * @return ParseFileContract
     * @throws Exception
     */
    private function getGenerateAdapter(): ParseFileContract
    {
        /**
         * In this method, I use a different approach to identify the correct adapter for file generation.
         * Based on the file extension I look for an adapter that can process the file correctly, so I don't need to make conditionals for each file type
         * When a new adapter is implemented, it automatically starts working, without the need to increment a conditional.
         *
         * The Laravel framework itself uses the same strategy to identify notification channels, this can be seen at:
         * @see \Illuminate\Notifications\RoutesNotifications::routeNotificationFor() line 43
         */
        if (empty($this->adapter)) {
            $extension = Str::studly(File::extension($this->filename));
            $className = "\App\Services\ParseFile\Adapters\Parse{$extension}";
            if (class_exists($className)) {
                return new $className();
            } else {
                throw new Exception("This type of file($extension) does not have an implementation for generating files");
            }
        }
        return $this->adapter;
    }
}
