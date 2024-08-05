<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use GuzzleHttp\Client;

class BookReturnedNotification extends Notification
{
    use Queueable;

    protected $title;
    protected $body;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
        $this->title = 'Book Returned';
        $this->body = 'Your book has been returned successfully';
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['fcm'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toFcm(object $notifiable)
    {

        $fcmToken = $notifiable->token_fcm;
        $client = new Client();
        $response = $client->post('https://fcm.googleapis.com/fcm/send', [
            'headers' => [
                'Authorization' => 'key=' . env('FCM_SERVER_KEY'),
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'to' => $fcmToken,
                'notification' => [
                    'title' => $this->title,
                    'body' => $this->body,
                ],
            ],
        ]);

        return $response;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
