<?php

namespace NotificationChannels\MobilyWs;

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
        $this->authMethod =  $this->config['authentication'];
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
}
