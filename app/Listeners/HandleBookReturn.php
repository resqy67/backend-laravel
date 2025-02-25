<?php

namespace App\Listeners;

use App\Events\BookReturn;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Laravel\Firebase\Facades\Firebase;

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

        // Send notification to user
        // $this->sendNotification($loan);
    }

    /**
     * Send notification to user
     */
    public function sendNotification($loan): void
    {
        // $messaging = Firebase::messaging();
        // $user = $loan->user;
        // if ($user->token_fcm) {
        //     Log::info("Sending FCM notification to user {$user->id} with token {$user->token_fcm}");
        //     try {
        //         $message = CloudMessage::withTarget('token', $user->token_fcm)
        //             ->withNotification([
        //                 'title' => 'Buku Telah Dikembalikan!',
        //                 'body' => 'Buku telah berhasil dikembalikan. Ayo pinjam buku lainnya! 📚',
        //             ]);
        //         $messaging->send($message);
        //     } catch (\Kreait\Firebase\Exception\MessagingException $e) {
        //         // Handle exception
        //         Log::error("FCM token for user {$user->id} is invalid: {$e->getMessage()}");
        //         $user->token_fcm = null;
        //         $user->save();
        //     } catch (\Kreait\Firebase\Exception\FirebaseException $e) {
        //         // Handle exception
        //         Log::error("An error occurred while sending FCM notification to user {$user->id}: {$e->getMessage()}");
        //     }
        // } else {
        //     Log::info("FCM token for user {$user->id} is not set");
        // }
    }
}
