<?php

namespace App\Services\ParseFile;

use Illuminate\Support\LazyCollection;

interface ParseFileContract
{
    /**
     *
     * @param string $filename
     * @return LazyCollection Return a LazyCollection from array of customers with their cards
     */
    public function parse(string $filename): LazyCollection;

    /**
     * @param string $filename
     * @param int $records
     * @return bool
     */
    public function generate(string $filename, int $records = 10): bool;
}
