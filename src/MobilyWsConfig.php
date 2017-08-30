<?php

namespace NotificationChannels\MobilyWs;

use NotificationChannels\MobilyWs\Exceptions\CouldNotSendNotification;

class MobilyWsConfig
{
    
    /**
     * @var string The authentication method
     */
    private $authMethod;
    
    /**
     * @var array
     */
    private $config;

    /**
     * MobilyWsConfig constructor.
     *
     * @param $config
     */
    public function __construct($config)
    {
        $this->config = $config;
        $this->setAuthenticationMethod($config);
    }
    
    public function getAuthenticationMethod()
    {
        return $this->authMethod;
    }

    public function __get($name)
    {
        if ($name == 'request') {
            return $this->config['guzzle']['request'];
        }

        if (isset($this->config[$name])) {
            return $this->config[$name];
        }
    }
    
    protected function setAuthenticationMethod($config)
    {
        if (isset($config['authentication'])) {
            $this->authMethod = $config['authentication'];
            return;
        }
        
        throw CouldNotSendNotification::withErrorMessage("Please set the authentication method in the mobilyws config file");
    
    }
}
