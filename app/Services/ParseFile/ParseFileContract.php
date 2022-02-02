<?php

namespace App\Services\ParseFile;

use App\Models\Customer;
use Illuminate\Support\LazyCollection;

interface ParseFileContract
{
    public function parse(string $filename): LazyCollection;

}
