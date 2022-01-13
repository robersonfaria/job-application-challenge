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

class ProcessCustomer implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    private $data;
    /**
     * @var false
     */
    private $onlyWith3ConsecutiveSameDigits;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data, $onlyWith3ConsecutiveSameDigits = false)
    {
        $this->data = $data;
        $this->onlyWith3ConsecutiveSameDigits = $onlyWith3ConsecutiveSameDigits;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $customer = new Customer($this->data);
            $card = new Card($this->data['credit_card']);

            CustomerService::validateAgeRange($customer);
            if ($this->onlyWith3ConsecutiveSameDigits) {
                CardService::validateExistsConsecutiveSameDigits($card);
            }
            // If necessary more rules can be added

            DB::beginTransaction();
            $customer->save();
            $customer->cards()->save($card);
            DB::commit();
        } catch (AgeRangeNotAllowedException | CardWithout3OrMoreConsecutiveSameDigitsException $e) {
            return; // It only interrupts the processing of this customer
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
        }
    }
}
