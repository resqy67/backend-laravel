<?php

namespace App\Console\Commands;

use App\Models\Book;
use App\Models\Loan;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Laravel\Firebase\Facades\Firebase;

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
        // buat variabel today yang berisi tanggal dan waktu yang ditambahkan 8 jam dari waktu saat ini
        $today = now()->addHours(8);
        // $today = now();
        $loans = Loan::where('return_date', '<=', $today)->get();

        $returnCount = 0;

        foreach ($loans as $loan) {
            Log::info("check token fcm for user {$loan->user->id}, token: {$loan->user->token_fcm}");
            // send notification to user
            $this->sendNotification($loan);

            $loan->delete();

            $book = Book::where('uuid', $loan->book_uuid)->first();
            if ($book) {
                $book->availability = true;
                $book->save();
                $returnCount++;
            }
        }

        $this->info('Books returned successfully. Total books returned: ' . $returnCount . ' books. ' . $today);
    }

    /**
     * Send notification to user
     */
    public function sendNotification($loan): void
    {
        $messaging = Firebase::messaging();
        $user = $loan->user;
        if ($user->token_fcm) {
            Log::info("Sending FCM notification to user {$user->id} with token {$user->token_fcm}");
            try {
                $message = CloudMessage::withTarget('token', $user->token_fcm)
                    ->withNotification([
                        'title' => 'Book Returned',
                        'body' => 'Your book has been returned successfully'
                    ]);
                $messaging->send($message);
            } catch (\Kreait\Firebase\Exception\MessagingException $e) {
                Log::error("Failed to send FCM notification to user {$user->id} with token {$user->token_fcm}");
            }
        }
    }
}
