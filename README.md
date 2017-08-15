# Laravel Mobily.ws Notification Channel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/alhoqbani/laravel-mobily-ws-notification.svg?style=flat-square)](https://packagist.org/packages/alhoqbani/laravel-mobily-ws-notification)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/alhoqbani/laravel-mobily-ws-notification/master.svg?style=flat-square)](https://travis-ci.org/alhoqbani/laravel-mobily-ws-notification)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/alhoqbani/laravel-mobily-ws-notification/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/alhoqbani/laravel-mobily-ws-notification/?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/alhoqbani/laravel-mobily-ws-notification.svg?style=flat-square)](https://packagist.org/packages/alhoqbani/laravel-mobily-ws-notification)

This package makes it easy to send notifications using [MobilyWs](https://www.mobily.ws) with Laravel 5.4.


## Contents

- [Installation](#installation)
	- [Package Installation](#package-installation)
	- [Set up mobily.ws account](#set-up-mobily.ws-account)
- [Usage](#usage)
	- [Credentials](#credentials)
	- [Create Notification](#create-notification)
	- [Routing SMS Notifications](#routing-sms-notifications)
	- [Sending SMS](#sending-sms)
	- [Scheduled SMS](#scheduled-sms)
	- [Available Message methods](#available-message-methods)
- [TODO](#todo)
- [Changelog](#changelog)
- [Testing](#testing)
- [Security](#security)
- [Contributing](#contributing)
- [License](#license)


## Installation

### Package Installation

Install the package using composer:
```bash
composer require alhoqbani/laravel-mobily-ws-notification
```
Add service provider to your array of providers in `config/app.php` 
> You don't need to do this step for laravel 5.5+
```php
        NotificationChannels\MobilyWs\MobilyWsServiceProvider::class,
```
Publish the configuration file:
```bash
php artisan vendor:publish --provider="NotificationChannels\MobilyWs\MobilyWsServiceProvider"
```

### Set up mobily.ws account
You must have an account with [MobilyWs](https://www.mobily.ws)  to be able to use this package.

> This package has no affiliation with mobily.ws whatsoever. 

#### Credentials.
You must add mobily.ws credentials to your `.env` file.

```
// Mobile number and password used for log in.
MOBILY_WS_MOBILE= 
MOBILY_WS_PASSWORD=
// name/number of the sender which must be approved by mobily.ws for GCC
MOBILY_WS_SENDER=
```

## Usage

### Create new notification:
Make a new notification class using laravel artisan
```bash
php artisan make:notification UserRegistered
``` 
and configure the notification class to use MobilyWsChannel.

Or you could use our custom artisan command:
```bash
php artisan mobilyws:notification UserRegistered
```

The `toMobilyWs` method should return a string of the text message to be sent or an instance of `MobilyWsMessage`.

See [Available Message methods](#available-message-methods) for more details.
```php
<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use NotificationChannels\MobilyWs\MobilyWsChannel;
use NotificationChannels\MobilyWs\MobilyWsMessage;

class UserRegistered extends Notification
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
     * Get the text message representation of the notification
     *
     * @param  mixed      $notifiable
     * @param \NotificationChannels\MobilyWs\MobilyWsMessage $msg
     *
     * @return \NotificationChannels\MobilyWs\MobilyWsMessage|string
     */
    public function toMobilyWs($notifiable, MobilyWsMessage $msg)
    {
        return "Dear $notifiable->name, welcome to our website";
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
`routeNotificationForMobilyWs` should return a mobile number to which the SMS message will be sent.

Please note that the mobile number must start with the country code without leading zeros.

For example, `9665xxxxxxxx`

### Sending SMS:
```php
use App\Notifications\UserRegistered;

$user = App\User::first();

$user->notify(new UserRegistered());
```

### Scheduled SMS
[MobilyWs](https://www.mobily.ws) Api allows for sending scheduled message which will be sent on the defined date/time.

> Please note that if you define time in the past, the message will be sent immediately by mobily.ws. 
This library will not check if the defined time is in the future.

You can define the time on which the message should be sent by mobily.ws by calling `time` method on the MobilyWsMessage instance.
```php
    public function toMobilyWs($notifiable)
    {
        return (new MobilyWsMessage)
            ->text("Message text")
            ->time(Carbon::parse("+1 week);
    }
```
The `time` method accepts either a DateTime object or a timestamp.

### Available Message methods
In your notification, you must define a method `toMobilyWs` which will receive the notifiable entity (e.g User model) and an instance of `MobilyWsMessage`. 

This method should return the text of the message to be sent as an SMS to mobily.ws or an instance of `MobilyWsMessage`. 

```php
<?php

use NotificationChannels\MobilyWs\MobilyWsMessage;
            //
    /**
     * Get the text message of the SMS.
     *
     * @param  mixed  $notifiable
     * @return \NotificationChannels\MobilyWs\MobilyWsMessage|string
     */
    public function toMobilyWs($notifiable)
    {
        return MobilyWsMessage::create("Text message");
    }
```
You can also pass the message to `MobilyWsMessage` constructor:

`return new MobilyWsMessage("Text message");`

or set the text message using the `msg()` method:
```php
    public function toMobilyWs($notifiable, MobilyWsMessage $msg)
    {
        return $msg->text($this->message);
    }
```
Method `toMobilyWs` will receive an instance of `MobilyWsMessage` as the 2nd argument.
#### list of available methods :
`text()` To add the content of the text message
'time()' To set time of the scheduled sms.

## TODO
- [ ] Validate mobile numbers
- [ ] Validate text messages type and length
- [ ] Validate given time is in the future.
- [x] Verify method `toMobilyWs` existence and config file.
- [x] Add the option to send Scheduled SMS
- [ ] Add the the rest of params (MsgID, msgKey, deleteKey, ~~timeSend~~, ~~dateSend~~)
- [ ] Translate mobily.ws error messages
- [ ] Create artisan command to made mobily.ws notifications
- [ ] Add list of fired event to the documentation.

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
