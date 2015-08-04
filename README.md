# Laravel Rackspace CloudQueue Driver [![Build Status](https://travis-ci.org/tailwind/laravel-rackspace-cloudqueue.svg?branch=master)](https://travis-ci.org/tailwind/laravel-rackspace-cloudqueue)
> **Note**: Currently there is only support for Laravel 4.2.

##Installation

### Install via Composer
Require this package in your composer.json and run composer update:

"tailwind/laravel-rackspace-cloudqueue": "~2.0"

### Add Configuration

```PHP
/// config/queue.php

return array(


    'default'     => 'rackspace',
    
    'connections' => array(

        'rackspace'  => [
            'driver'   => 'rackspace',
            'queue'    => 'default_tube', /// the default queue
            'endpoint' => 'US',  /// US or UK
            'username' => 'SOME_RACKSPACE_USERNAME',
            'apiKey'   => 'SOME_RACKSPACE_API_KEY',
            'region'   => 'ORD', /// THE REGION WHERE THE QUEUE IS SETUP
            'urlType'  => 'internalURL', /// Optional, defaults to internalURL
        ]

    ),

);,

```

### Add Service Provider

```PHP
/// config/app.php

return array(

    'providers'  => array(
        'Tailwind\RackspaceCloudQueue\RackspaceCloudQueueServiceProvider'
    ),
);

```
	
	

## Changelog

### Version 2.0.2
* Fix a bug where 10 items were popped off a time and only 1 was processed

### Version 2.0
* Change to Tailwind namespace
* Shorten driver name to just "rackspace"
* Add support for specifying a tube besides the default
* Add to Packagist
* Add test suite

### Version 1.0
* Transfer from [cchiles](https://github.com/cchiles) to Tailwind
* Support for RackspaceCloudQueue
