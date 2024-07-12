<?php

namespace App\Listeners;

use App\Events\LoanCreated;
use App\Models\LoanHistory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SaveLoanHistory
{
    /**
     * Create the event listener.
     */
    // public $loan;

    public function __construct()
    {
        // $this->loan = $loan;
    }

    /**
     * Handle the event.
     */
    // public function handle(object $event): void
    // {
    //     //
    // }
    public function handle(LoanCreated $event): void
    {
        //

        // Save loan history
        LoanHistory::create([
            'user_id' => $event->loan->user_id,
            'book_uuid' => $event->loan->book_uuid,
            'loan_date' => $event->loan->loan_date,
            'return_date' => $event->loan->return_date,
        ]);
    }
}
