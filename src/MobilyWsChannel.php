<?php

namespace NotificationChannels\MobilyWs;

use Illuminate\Events\Dispatcher;
use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Notifications\Notification;
use NotificationChannels\MobilyWs\Exceptions\CouldNotSendNotification;

class MobilyWsChannel
{
    /** @var MobilyWsApi */
    private $api;

    /** @var Dispatcher */
    private $events;

    /**
     * MobilyWsChannel constructor.
     *
     * @param MobilyWsApi                   $mobilyWs
     * @param \Illuminate\Events\Dispatcher $events
     */
    public function __construct(MobilyWsApi $mobilyWs, Dispatcher $events)
    {
        $this->api = $mobilyWs;
        $this->events = $events;
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
        $number = $notifiable->routeNotificationFor('MobilyWs') ?: $notifiable->phone_number;

        $response = $this->api->send([
            'msg' => $notification->toMobilyWs($notifiable),
            'numbers' => $number,
        ]);

        if ($response['code'] == 1) {
            return $response['message'];
        }
        $this->events->fire(
            new NotificationFailed($notifiable, $notification, 'mobily-ws', $response)
        );
    }
}
