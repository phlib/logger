# phlib/logger

[![Code Checks](https://img.shields.io/github/workflow/status/phlib/logger/CodeChecks?logo=github)](https://github.com/phlib/logger/actions/workflows/code-checks.yml)
[![Codecov](https://img.shields.io/codecov/c/github/phlib/logger.svg?logo=codecov)](https://codecov.io/gh/phlib/logger)
[![Latest Stable Version](https://img.shields.io/packagist/v/phlib/logger.svg?logo=packagist)](https://packagist.org/packages/phlib/logger)
[![Total Downloads](https://img.shields.io/packagist/dt/phlib/logger.svg?logo=packagist)](https://packagist.org/packages/phlib/logger)
![Licence](https://img.shields.io/github/license/phlib/logger.svg)

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
            // logger type (stream, gelf...)
            'type'  => 'stream',
            // the level of log messages to include (optional)
            'level' => \Psr\Log\LogLevel::ERROR, 
             // logger specific parameters
            'path'  => '/var/log/my_app.log'
        ],
        [
            'type'  => 'gelf',                
            'level' => \Psr\Log\LogLevel::INFO,
             // logger specific parameters
            'host'  => '127.0.0.1',
            'port'  => 12201
        ]
    ],
    'application' => 'default', // alias to another logger config
    'api' => [
        'type' => 'gelf',
        'host' => '127.0.0.1',
        'port' => 12201 
    ]
];

```

Creation of logger pool

``` php
$loggerPool = new \Phlib\Logger\Pool(
    new \Phlib\Logger\Config($loggerConfig), 
    new \Phlib\Logger\Factory()
);

```

Get a logger instance
``` php
$applicationLogger = $loggerPool->getLogger('application');
// or
$applicationLogger = $loggerPool->application;

$applicationLogger->info('Logging is initialised');

```

## License

This package is free software: you can redistribute it and/or modify
it under the terms of the GNU Lesser General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Lesser General Public License for more details.

You should have received a copy of the GNU Lesser General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
