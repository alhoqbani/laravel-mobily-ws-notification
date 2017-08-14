<?php

namespace NotificationChannels\MobilyWs;

use Illuminate\Notifications\Notification;

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
     */
    public function send($notifiable, Notification $notification)
    {
        $number = $notifiable->routeNotificationFor('MobilyWs') ?: 'phone_number';

        return $this->api->send([
            'msg' =>    $notification->toMobilyWs($notifiable),
            'numbers' => $notifiable->{$number},
        ]);
    }
}
