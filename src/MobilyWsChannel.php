<?php

namespace NotificationChannels\MobilyWs;

use NotificationChannels\MobilyWs\Exceptions\CouldNotSendNotification;
use Illuminate\Notifications\Notification;

class MobilyWsChannel
{
    public function __construct()
    {
        // Initialisation code here
    }

    /**
     * Send the given notification.
     *
     * @param mixed                                  $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     *
     * @throws CouldNotSendNotification
     */
    public function send($notifiable, Notification $notification)
    {
        //$response = [a call to the api of your notification send]

//        if ($response->error) { // replace this by the code need to check for errors
//            throw CouldNotSendNotification::serviceRespondedWithAnError($response);
//        }
    }
}
