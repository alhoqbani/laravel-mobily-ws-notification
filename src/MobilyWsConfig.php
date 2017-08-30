<?php

namespace NotificationChannels\MobilyWs;

use NotificationChannels\MobilyWs\Exceptions\CouldNotSendMobilyWsNotification;

class MobilyWsConfig
{
    /**
     * @var array Supported authentication methods
     */
    protected $authenticationMethods = [
        'apiKey', 'password', 'auto',
    ];

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
        $this->validateCredentials();
    }

    public function getCredentials()
    {
        switch ($this->authMethod) {
            case 'password':
                return [
                  'mobile' => $this->mobile,
                  'password' => $this->password,
                ];
            case 'apiKey':
                return [
                  'apiKey' => $this->apiKey,
                ];
            case 'auto':
                return $this->getAutoCredentials();
        }
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
            if (in_array($config['authentication'], $this->authenticationMethods)) {
                return $this->authMethod = $config['authentication'];
            }

            throw CouldNotSendMobilyWsNotification::withErrorMessage(
                sprintf('Method %s is not supported. Please choose from: (apiKey, password, auto)',
                    $config['authentication']
                )
            );
        }

        throw CouldNotSendMobilyWsNotification::withErrorMessage('Please set the authentication method in the mobilyws config file');
    }

    protected function getAutoCredentials()
    {
        if ($this->apiKey) {
            return [
              'apiKey' => $this->apiKey,
            ];
        }

        return [
            'mobile' => $this->mobile,
            'password' => $this->password,
        ];
    }

    protected function validateCredentials()
    {
        if (!isset($this->config['apiKey']) && !isset($this->config['mobile'], $this->config['password'])) {
            throw CouldNotSendMobilyWsNotification::withErrorMessage('No credentials were provided. Please set your (mobile/password) or apiKey in the config file');
        }
    }
}
