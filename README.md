Here's the latest documentation on Laravel 5.4 Notifications System: 

https://laravel.com/docs/master/notifications

# Laravel Mobily.ws Notification Channel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/alhoqbani/laravel-mobily-ws-notification.svg?style=flat-square)](https://packagist.org/packages/alhoqbani/laravel-mobily-ws-notification)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/alhoqbani/laravel-mobily-ws-notification/master.svg?style=flat-square)](https://travis-ci.org/alhoqbani/laravel-mobily-ws-notification)
[![StyleCI](https://styleci.io/repos/100258454/shield)](https://styleci.io/repos/100258454)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/:sensio_labs_id.svg?style=flat-square)](https://insight.sensiolabs.com/projects/:sensio_labs_id)
[![Quality Score](https://img.shields.io/scrutinizer/g/alhoqbani/laravel-mobily-ws-notification.svg?style=flat-square)](https://scrutinizer-ci.com/g/alhoqbani/laravel-mobily-ws-notification)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/alhoqbani/laravel-mobily-ws-notification/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/alhoqbani/laravel-mobily-ws-notification/?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/alhoqbani/laravel-mobily-ws-notification.svg?style=flat-square)](https://packagist.org/packages/alhoqbani/laravel-mobily-ws-notification)

This package makes it easy to send notifications using [MobilyWs](https://www.mobily.ws) with Laravel 5.4.


## Contents

- [Installation](#installation)
	- [Setting up the MobilyWs service](#setting-up-the-MobilyWs-service)
- [Usage](#usage)
	- [Available Message methods](#available-message-methods)
- [TODO](#todo)
- [Changelog](#changelog)
- [Testing](#testing)
- [Security](#security)
- [Contributing](#contributing)
- [License](#license)


## Installation
Install using composer:
```bash
composer require alhoqbani/laravel-mobily-ws-notification
```
Add service provider to your array of providers in `config/app.php`
```php
        NotificationChannels\MobilyWs\MobilyWsServiceProvider::class,
```
Publish the configuration file:
```bash
php artisan vendor:publish --provider="NotificationChannels\MobilyWs\MobilyWsServiceProvider"
```
### Setting up the Mobily.ws account

You must have an account with [MobilyWs](https://www.mobily.ws)  to be able to use this package.

> This package has no affiliation with mobily.ws whatsoever. 

## Usage
### Add your mobily.ws credentials to your `.env` file.
```php
MOBILY_WS_MOBILE=
MOBILY_WS_PASSWORD=
// Name/Number of Sender must be approved by mobily.ws for GCC
MOBILY_WS_SENDER=
```

### Make a new notification class using laravel artisan:
```bash
php artisan make:notification SmsNewUser
``` 
### Configure the notification class to use MobilyWsChannel:

The `toMobilyWs` method should return a string of the text message to be sent.
```php
<?php

namespace App\Notifications;

use NotificationChannels\MobilyWs\MobilyWsChannel;
use Illuminate\Notifications\Notification;

class SmsNewUser extends Notification
{
    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [MobilyWsChannel::class];
    }
    
    /**
     * Get the text message of the SMS.
     *
     * @param  mixed  $notifiable
     * @return string 
     */
    public function toMobilyWs($notifiable)
    {
        return "Dear $notifiable->name , Thank for your business with us";
    }
}
```

### Routing SMS Notifications:

When sending notifications via the `MobilyWs` channel, the notification system will automatically look for a `phone_number` attribute on the notifiable entity.
If you would like to customize the phone number the notification is delivered to, define a `routeNotificationForMobilyWs` method on the entity:

```php
<?php

    namespace App;

    use Illuminate\Notifications\Notifiable;
    use Illuminate\Foundation\Auth\User as Authenticatable;

    class User extends Authenticatable
    {
        use Notifiable;

        /**
         * Route notifications for the MobilyWs channel.
         *
         * @return string
         */
        public function routeNotificationForMobilyWs()
        {
            return $this->mobile;
        }
    }
```

Please note that the mobile number must start with the country code without leading zeros.

For example, `9665xxxxxxxx`

### Sending SMS:
```php
use App\Notifications\SmsNewUser;

$user->notify(new SmsNewUser());
```

## TODO
- [ ] Validate mobile numbers
- [ ] Validate text messages' type and length

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
