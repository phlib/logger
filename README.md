# phlib/logger

[![Latest Version](https://img.shields.io/github/release/phlib/logger.svg?style=flat-square)](https://github.com/phlib/logger/releases)
[![Build Status](https://img.shields.io/travis/phlib/logger/master.svg?style=flat-square)](https://travis-ci.org/phlib/logger)
[![Total Downloads](https://img.shields.io/packagist/dt/phlib/logger.svg?style=flat-square)](https://packagist.org/packages/phlib/logger)

PHP PSR-3-compatible logger

## Install

Via Composer

``` bash
$ composer require phlib/logger
```

## Usage

Example config for a logger pool

``` php
$loggerConfig = [
    'default' => [ // logger config identifier, used as facility/name in log messages
        // multiple logger entries becomes a collection logger
        [
            // name of the logger type (stream, gelf...)
            'name'  => 'stream',
            // the level of log messages to include (optional)
            'level' => \Psr\Log\LogLevel::ERROR, 
             // logger specific parameters
            'path'  => '/var/log/my_app.log'
        ],
        [
            'name'  => 'gelf',                
            'level' => \Psr\Log\LogLevel::INFO,
             // logger specific parameters
            'host'  => '127.0.0.1',
            'port'  => 12201
        ]
    ],
    'application' => 'default', // alias to another logger config
    'api' => [
        'name' => 'gelf',
        'host' => '127.0.0.1',
        'port' => 12201 
    ]
];

```

Creation of logger pool

``` php
$loggerPool = new \Phlib\Logger\Pool($loggerConfig, new \Phlib\Logger\Factory());

```

Get a logger instance
``` php
$applicationLogger = $loggerPool->getLogger('application');
// or
$applicationLogger = $loggerPool->application;

$applicationLogger->info('Logging is initialised');

```