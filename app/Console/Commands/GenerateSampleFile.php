<?php

namespace App\Console\Commands;

use App\Services\ParseFile\ParseFile;
use Illuminate\Console\Command;

class GenerateSampleFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'challenge:generate-file
        {--r|records=10 : Number of records}
        {filename : Name of the file to be generated with extension}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate fake files in json, csv and xml format with fake data';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            (new ParseFile($this->argument('filename')))
                ->generateFile($this->option('records'));
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            $this->error('See log files for details!');
            report($e);
        }
        return 0;
    }
}
