<?php

namespace NotificationChannels\MobilyWs;

use Carbon\Carbon;
use DateTime;
use DateTimeInterface;
use NotificationChannels\MobilyWs\Exceptions\CouldNotSendNotification;

class MobilyWsMessage
{
    /** @var string */
    public $text;

    /** @var Carbon */
    public $time;

    /**
     * Create new instance of mobilyWsMessage.
     *
     * @param string $text
     *
     * @return static
     */
    public static function create($text = '')
    {
        return new static($text);
    }

    /**
     * MobilyWsMessage constructor.
     *
     * @param string $text
     */
    public function __construct($text = '')
    {
        $this->text = $text;
    }

    /**
     * Set the Content of the SMS message.
     *
     * @param $text
     *
     * @return $this
     */
    public function text($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @param DateTime|Carbon|int $time
     *
     * @return $this
     *
     * @throws CouldNotSendNotification
     */
    public function time($time)
    {
        if ($time instanceof DateTimeInterface) {
            return $this->time($time->getTimestamp());
        }

        if (is_numeric($time)) {

            $this->time = Carbon::createFromTimestamp($time);

            return $this;
        }

        throw CouldNotSendNotification::withErrorMessage(
            sprintf('Time must be a timestamp or an object implementing DateTimeInterface. %s is given', gettype($time))
        );
    }
}
