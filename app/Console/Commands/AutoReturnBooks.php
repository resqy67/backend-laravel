<?php

namespace App\Console\Commands;

use App\Models\Book;
use App\Models\Loan;
use Illuminate\Console\Command;
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
    public function sendNotification($loan): void
    {
        $messaging = Firebase::messaging();
        $user = $loan->user;
        if ($user->token_fcm) {
            // Log::info("Sending FCM notification to user {$user->id} with token {$user->token_fcm}");
            try {
                $message = CloudMessage::withTarget('token', $user->token_fcm)
                    ->withNotification([
                        'title' => 'Book Returned',
                        'body' => 'Your book has been returned successfully'
                    ]);
                $messaging->send($message);
            } catch (\Kreait\Firebase\Exception\MessagingException $e) {
                // Handle exception
                // Log::error("FCM token for user {$user->id} is invalid: {$e->getMessage()}");
                $user->token_fcm = null;
                $user->save();
            } catch (\Kreait\Firebase\Exception\FirebaseException $e) {
                // Handle exception
                // Log::error("An error occurred while sending FCM notification to user {$user->id}: {$e->getMessage()}");
            }
        } else {
            // Log::info("FCM token for user {$user->id} is not set");
        }
    }
}
