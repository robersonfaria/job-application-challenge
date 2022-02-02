<?php

namespace App\Jobs;

use App\Exceptions\AgeRangeNotAllowedException;
use App\Exceptions\CardWithout3OrMoreConsecutiveSameDigitsException;
use App\Models\Card;
use App\Models\Customer;
use App\Services\CardService;
use App\Services\CustomerService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ProcessCustomerJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    /**
     * @var array
     */
    private $data;

    /**
     * @var bool
     */
    private bool $onlyWith3ConsecutiveSameDigits;

    /**
     * Create a new job instance.
     *
     * @param array $data
     * @param bool $onlyWith3ConsecutiveSameDigits
     */
    public function __construct(array $data, bool $onlyWith3ConsecutiveSameDigits = false)
    {
        $this->data = $data;
        $this->onlyWith3ConsecutiveSameDigits = $onlyWith3ConsecutiveSameDigits;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws AgeRangeNotAllowedException
     * @throws CardWithout3OrMoreConsecutiveSameDigitsException
     */
    public function handle()
    {
        DB::beginTransaction();
        try {
            /**
             * fill model objects
             */
            $customer = new Customer($this->data);
            $card = new Card($this->data['credit_card']);

            /**
             * Validate data
             *
             * If necessary more rules can be added
             */
            CustomerService::validateAgeRange($customer);
            if ($this->onlyWith3ConsecutiveSameDigits) {
                CardService::validateExistsConsecutiveSameDigits($card);
            }

            $customer->save();
            $customer->cards()->save($card);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
