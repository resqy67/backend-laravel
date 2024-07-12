<?php

namespace App\Listeners;

use App\Events\BookAdded;
use App\Notifications\PushNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\User;

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
    public function handle(BookAdded $event): void
    {
        //
        $users = User::all();

        foreach ($users as $user) {
            if ($user->fcm_token) {
                $user->notify(new PushNotification('Book Added', 'A new book has been added'));
            }
        }
    }
}
