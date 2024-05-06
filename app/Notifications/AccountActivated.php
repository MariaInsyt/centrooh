<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class AccountActivated extends Notification
{
    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [FcmChannel::class];
    }

    public function toFcm(object $notifiable): FcmMessage
    {
        // return (new FcmMessage(notification: new FcmNotification(
        //     title: 'Account Activated',
        //     body: 'Your Insyt Media Agent Account has been activated.',
        //     image: ''
        // )));
        $message = new FcmMessage();
        $message->content([
            'title' => 'Account Activated',
            'body' => 'Your Insyt Media Agent Account has been activated.',
        ]);

        return $message;
    }
}
