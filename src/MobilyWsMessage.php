<?php

namespace NotificationChannels\MobilyWs;

class MobilyWsMessage
{
    /** @var string */
    public $msg;

    /**
     * Create new instance of mobilyWsMessage.
     *
     * @param string $msg
     *
     * @return static
     */
    public static function create($msg = '')
    {
        return new static($msg);
    }

    /**
     * MobilyWsMessage constructor.
     *
     * @param string $msg
     */
    public function __construct($msg = '')
    {
        $this->msg = $msg;
    }

    /**
     * Set the Content of the SMS message.
     *
     * @param $msg
     *
     * @return $this
     */
    public function msg($msg)
    {
        $this->msg = $msg;

        return $this;
    }
}
