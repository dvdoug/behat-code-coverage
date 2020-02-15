# Changelog

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
### Fixed
 - Support for `phpdbg`
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
