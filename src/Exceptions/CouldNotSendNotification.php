<?php

namespace NotificationChannels\MobilyWs\Exceptions;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\RequestException;

class CouldNotSendNotification extends \Exception
{
    /**
     * Thrown when mobily.ws return a response body other than '1'.
     *
     * @param $code
     * @param $message
     *
     * @return static
     */
    public static function mobilyWsRespondedWithAnError($code, $message)
    {
        return new static(
            sprintf("Mobily.ws responded with error number %s and message:\n%s",
                $code,
                $message
            ));
    }

    /**
     * Thrown when GuzzleHttp throw a request exception.
     *
     * @param RequestException $exception
     *
     * @return static
     */
    public static function couldNotSendRequestToMobilyWs(RequestException $exception)
    {
        return new static(
            'Request to mobily.ws failed',
            $exception->getCode(),
            $exception
        );
    }

    /**
     * Thrown when any other errors occur.
     *
     * @param Response $response
     *
     * @return static
     */
    public static function someErrorWhenSendingSms(Response $response)
    {
        $code = $response->getStatusCode();
        $message = $response->getBody()->getContents();

        return new static(
            sprintf('Could not send sms notification to mobily.ws. Status code %s and message: %s', $code, $message)
        );
    }
}
