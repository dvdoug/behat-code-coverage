# Changelog

## [5.0.0] - 2020-xx-xx
### Changed
 - Minimum version of PHP supported is `7.3`
### Removed
 - The `RemoteXDebug` driver, it was a companion to an old Symfony bundle, not a generally-usable feature
 - The old `report` configuration key, use `reports` instead
 - Removed `forceCoversAnnotation` and `mapTestClassNameToCoveredClassName` configuration keys, these options are not supported by `php-code-coverage anymore`
 - Removed the custom driver selection logic and replaced it with built-in logic from `php-code-coverage`. This means that PCOV is now supported
 - Removed legacy `LeanPHP\Behat\CodeCoverage` alias

## [4.1.1] - 2020-02-15
### Added
 - Compatibility with `phpunit/php-code-coverage` v8

## [4.1.0] - 2019-11-04
### Added
 - Added `reports` configuration key to enable generation of multiple coverage output formats, with schema validation of the available format-specific options
### Deprecated
 - The `report` configuration key as it only allowed for a single report type

## [4.0.1] - 2019-08-04
### Added
 - Added back support for `LeanPHP\Behat\CodeCoverage` in `behat.yml` for seamless drop-in of the fork
 - Support for `phpdbg`
### Fixed
 - Issue with directories containing dashes in the name
 - Issue with `xdebug` and `delete` calls

## [4.0.0] - 2019-08-04
### Added
 - Support for version 7.0 of `phpunit/php-code-coverage`
### Changed
 - Changed namespace of all code to `LeanPHP\Behat\CodeCoverage` from `DVDoug\Behat\CodeCoverage`
 - Minimum version of PHP supported is `7.1`
### Removed
 - Support for Symfony components older than `<3.4`
 - Support for HHVM

[Unreleased]: https://github.com/dvdoug/behat-code-coverage/compare/v4.1.1...master
[4.1.1]: https://github.com/dvdoug/behat-code-coverage/compare/v4.1.0...v4.1.1
[4.1.0]: https://github.com/dvdoug/behat-code-coverage/compare/v4.0.1...v4.1.0
[4.0.1]: https://github.com/dvdoug/behat-code-coverage/compare/v4.0.0...v4.0.1
[4.0.0]: https://github.com/dvdoug/behat-code-coverage/compare/v3.4.1...v4.0.0
