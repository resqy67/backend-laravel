<?php

namespace App\Console\Commands;

use App\Models\Book;
use App\Models\Loan;
use Illuminate\Console\Command;

class AutoReturnBooks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // protected $signature = 'app:auto-return-books';
    protected $signature = 'books:auto-return';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Return books automatically after 7 days of loan date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = now();
        $loans = Loan::where('return_date', '<=', $today)->get();

        $returnCount = 0;

        foreach ($loans as $loan) {
            $loan->delete();

            $book = Book::where('uuid', $loan->book_uuid)->first();
            if ($book) {
                $book->availability = true;
                $book->save();
                $returnCount++;
            }
        }

        $this->info('Books returned successfully. Total books returned: ' . $returnCount);
    }
}
