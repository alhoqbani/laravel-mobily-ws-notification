<?php

namespace NotificationChannels\MobilyWs;

class MobilyWsMessage
{
    /** @var string */
    public $text;

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
}
