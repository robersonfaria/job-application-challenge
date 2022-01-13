<?php

namespace App\Console\Commands;

use App\Services\Utils\GenerateFile;
use Illuminate\Console\Command;

class GenerateSampleFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'challenge:generate-file
        {--t|type=json : Data format}
        {--r|records=10 : Number of records}
        {filename : Name of the file to be generated without the extension}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate files in json, csv and xml format with fake data';

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
            $generator = new GenerateFile(
                $this->option('type'),
                $this->argument('filename'),
                $this->option('records')
            );
            $generator->run();
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            $this->error('See log files for details!');
            report($e);
        }
        return 0;
    }
}
