# Change Log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) 
and this project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]

## [3.0.1] - 2018-02-13
### Added
- Apply GNU LGPLv3 software licence
- Add support for Symfony\Console v4

## [3.0.0] - 2016-10-27
### Added
- Custom logger decorators can be registered on the Factory and applied via config
- New `Decorator\DefaultContext` which allows default context values to be set for all logs.
  Use directly or add to config, eg. `'defaultContext' => ['foo'=>'bar']`

### Changed
- Renamed `Collection` to `LoggerType\Collection`
- Renamed `Stream` to `LoggerType\Stream`
- Renamed `CliColor` to `LoggerType\CliColor`
- Renamed `LevelFilter` to `Decorator\LevelFilter`

## [2.1.1] - 2016-10-27
### Added
- Mark package as providing an implementation of `\Psr\Log` by using Composer `provide` option

## [2.1.0] - 2016-04-29
### Added
- CliColor logger which can be manually constructed to add coloured log output to your CLI scripts' verbose mode.
- Officially support php70

## [2.0.1] - 2016-03-21
### Added
- Support for additional handlers by extending Factory

## [2.0.0] - 2016-03-21
### Added
- New Config class to separate the logic for interpreting the pool's config. Changes the Pool's constructor.

### Changed
- Config key `name` renamed to `type`

## [1.1.0] - 2015-08-26
### Added
- New Pool method to allow user to always get a collection logger

## [1.0.1] - 2015-08-25
### Added
- README.md

### Changed
- Composer lock file is no longer committed, to avoid [dependency hell](https://philsturgeon.uk/php/2014/11/04/composer-its-almost-always-about-the-lock-file/)

## [1.0.0] - 2015-08-25
Initial Release
