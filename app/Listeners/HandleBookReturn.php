<?php

namespace App\Listeners;

use App\Events\BookReturn;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandleBookReturn
{

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(BookReturn $event): void
    {
        //
        $loan = $event->loan;

        // Update return_date to current date
        $loan->return_date = now();
        $loan->save();

        // Update book availability
        $book = $loan->book;
        $book->availability = true;
        $book->save();
    }
}
