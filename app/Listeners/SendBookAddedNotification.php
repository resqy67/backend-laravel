<?php

namespace App\Listeners;

use App\Events\BookAdded;
use App\Notifications\PushNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\User;
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
        $messaging = Firebase::messaging();
        $users = User::all();

        foreach ($users as $user) {
            if ($user->token_fcm) {
                // $user->notify(new PushNotification('Book Added', 'A new book has been added. Check it out now! 📚' . $event->book->title));
                $message = CloudMessage::withTarget('token', $user->token_fcm)
                    ->withNotification([
                        'title' => 'Book Added',
                        'body' => 'A new book has been added. Check it out now! 📚' . $event->book->title,
                    ]);
                $messaging->send($message);
            }
        }
    }
}
