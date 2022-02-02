<?php

namespace App\Console\Commands;

use App\Jobs\ProcessCustomer;
use App\Services\ParseFile\ParseFile;
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
    protected $description = 'Process file and persist the data in the database(json, csv, xml)';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        /**
         * Batch queues first serialize and queue all jobs and then dispatch, if an error occurs in the queue no job is
         * executed. And because it is a queue, if during the processing of jobs the execution is interrupted, when the
         * jobs return, the queue will naturally continue to be processed.
         */
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

            (new ParseFile($this->option('file')))
                ->parse()
                ->unique() // filter unique registers
                ->each(function ($data) {
                    /**
                     * For each line of the file to be processed, a job will be scheduled.
                     */
                    $this->bus->add([new ProcessCustomer($data, $this->option('3digits'))]);
                });
            /**
             * Only after all jobs are queued is processing dispatched.
             */
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
