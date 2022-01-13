<?php

namespace App\Console\Commands;

use App\Jobs\ProcessCustomer;
use App\Services\Utils\ParseFile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class ProcessFile extends Command
{

    /**
     * @var \Illuminate\Bus\PendingBatch
     */
    private $bus;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = '
        challenge:process-file
        {--f|file=challenge.json : File name with extension, file must be in storage/app folder}
        {--3digits : Only record customer where the card has 3 identical consecutive digits.}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process the JSON file and persist the data in the database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->bus = Bus::batch([]);
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
            $this->info('Starting batch creation.');

            $service = new ParseFile($this->option('file'));
            $service->parse()
                ->unique()
                ->each(function ($data) {
                    $this->bus->add([new ProcessCustomer($data, $this->option('3digits'))]);
                });
            $this->bus->dispatch();
            $this->info('Batch creation completed, starting data processing (queued)');
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            $this->error('See log files for details!');
            report($e);
        }
        return 0;
    }
}
