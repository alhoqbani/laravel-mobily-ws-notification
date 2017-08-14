<?php

namespace NotificationChannels\MobilyWs;

use Illuminate\Notifications\Notification;
use NotificationChannels\MobilyWs\Exceptions\CouldNotSendNotification;

class MobilyWsChannel
{
    /** @var MobilyWsApi */
    private $api;

    /**
     * MobilyWsChannel constructor.
     *
     * @param MobilyWsApi $mobilyWs
     */
    public function __construct(MobilyWsApi $mobilyWs)
    {
        $this->api = $mobilyWs;
    }

    /**
     * Send the given notification.
     *
     * @param mixed        $notifiable
     * @param Notification $notification
     *
     * @return string
     *
     * @throws \NotificationChannels\MobilyWs\Exceptions\CouldNotSendNotification
     */
    public function send($notifiable, Notification $notification)
    {
        $number = $notifiable->routeNotificationFor('MobilyWs') ?: 'phone_number';

        $response = $this->api->send([
            'msg' => $notification->toMobilyWs($notifiable),
            'numbers' => $notifiable->{$number},
        ]);
        
        if ($response['code'] == 1) {
            return $response['message'];
        }
        throw CouldNotSendNotification::mobilyWsRespondedWithAnError($response['code'], $response['message']);
    }
}
