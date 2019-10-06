# Changelog

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

[Unreleased]: https://github.com/dvdoug/behat-code-coverage/compare/v4.0.1...master
[4.0.1]: https://github.com/dvdoug/behat-code-coverage/compare/v4.0.0...v4.0.1
[4.0.0]: https://github.com/dvdoug/behat-code-coverage/compare/v3.4.1...v4.0.0
