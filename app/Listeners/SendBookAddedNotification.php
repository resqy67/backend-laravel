<?php

namespace App\Listeners;

use App\Events\BookAdded;
use App\Notifications\PushNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Laravel\Firebase\Facades\Firebase;

class SendBookAddedNotification
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
    public function handle(BookAdded $event)
    {
        //
        // $messaging = Firebase::messaging();
        // $users = User::all();

        // foreach ($users as $user) {
        //     if ($user->token_fcm) {
        //         // $user->notify(new PushNotification('Book Added', 'A new book has been added. Check it out now! 📚' . $event->book->title));
        //         try {
        //             $message = CloudMessage::withTarget('token', $user->token_fcm)
        //                 ->withNotification([
        //                     'title' => 'Buku Baru Saja Ditambahkan!',
        //                     'body' => 'Buku baru telah ditambahkan. Cek sekarang! 📚 ',
        //                 ]);
        //             $messaging->send($message);
        //         } catch (\Kreait\Firebase\Exception\MessagingException $e) {
        //             // Handle exception
        //             Log::error("FCM token for user {$user->id} is invalid: {$e->getMessage()}");
        //             $user->token_fcm = null;
        //             $user->save();
        //         } catch (\Kreait\Firebase\Exception\FirebaseException $e) {
        //             // Handle exception
        //             Log::error("An error occurred while sending FCM notification to user {$user->id}: {$e->getMessage()}");
        //         }
        //     }
        // }
    }
}
