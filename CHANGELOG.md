# Changelog

## [Unreleased] - 2025-xx-xx

## [5.3.7] - 2025-02-07
### Added
- Compatibility with `phpunit/php-code-coverage` v12

## [5.3.6] - 2024-11-16
### Fixed
- Don't show any warning messages about missing coverage driver when `--no-coverage` CLI option is used

## [5.3.5] - 2024-09-22
### Changed
- Use `stderr` instead of `stdout` for warnings

## [5.3.4] - 2024-04-03
### Fixed
- Better handling of relative paths

## [5.3.3] - 2024-02-03
### Added
- Compatibility with `phpunit/php-code-coverage` v11

### Changed
- Minimum `phpunit/php-code-coverage` version bumped to 10.1

## [5.3.2.1] - 2024-09-15
### Fixed
- Better handling of PCOV and Xdebug running side by side

## [5.3.2] - 2023-12-09
### Added
- Support for Symfony 7

### Removed
- Support for Symfony 4

## [5.3.1] - 2023-05-08
### Changed
- Stopped using some deprecated methods inside `phpunit/php-code-coverage` v10.1

### Fixed
- Better handling of PCOV and Xdebug running side by side

## [5.3.0] - 2023-02-04

### Added
- Compatibility with `phpunit/php-code-coverage` v10
- Added `colors` and `customCSSFile` options to HTML report configuration

## [5.2.2] - 2021-11-30
### Added
- Support for Symfony 6

### Changed
- Improved error messages when a coverage driver cannot be found
- Deprecated `processUncoveredFiles` setting, it has been removed as of php-code-coverage v10.

## [5.2.1] - 2021-01-10
### Fixed
- When Xdebug was enabled, but its coverage feature was disabled an exception was thrown. This scenario is now treated
  the same as when no coverage driver is loaded at all (a warning is printed but Behat is allowed to run to completion)

## [5.2.0] - 2020-10-11
### Added
 - Added support for the Cobertura report format

### Changed
 - Minimum `phpunit/php-code-coverage` version bumped to 9.2

## [5.1.1] - 2020-08-14
### Fixed
 - Make the `--no-coverage` option work again

## [5.1.0] - 2020-08-10
### Added
 - Support for `phpunit/php-code-coverage`'s static analysis cache introduced in v9.1. This can be configured via the `cache` key in behat.yml, otherwise defaults to `sys_get_temp_dir() . '/behat-code-coverage-cache'`

### Changed
 - Minimum `phpunit/php-code-coverage` version bumped to 9.1

### Removed
 - Support for Symfony 3.4, in alignment with https://github.com/Behat/Behat/issues/1296

## [5.0.1] - 2021-09-13
### Fixed
 - Make the `--no-coverage` option work again

## [5.0.0] - 2020-08-07
### Added
 - Compatibility with `phpunit/php-code-coverage` v9. Branch and path coverage is automatically enabled when running under Xdebug. For more information on this feature, see https://doug.codes/php-code-coverage
 - `branchAndPathCoverage` configuration key to enable/disable path and branch coverage. Setting this to `true` explicitly will warn when the feature cannot be used.
 - Support for PCOV

### Removed
 - The old `report` configuration key, use `reports` instead
 - Removed `forceCoversAnnotation` and `mapTestClassNameToCoveredClassName` configuration keys, these options are not supported by `php-code-coverage` anymore
 - Removed the `whitelist` configuration key to align with `php-code-coverage` v9 terminology. All former subkeys of `whitelist` are now subkeys of `filter`
 - Renamed `addUncoveredFilesFromWhitelist` and `processUncoveredFilesFromWhitelist` to `includeUncoveredFiles` and `processUncoveredFiles` to align with `php-code-coverage` v9 terminology
 - Removed the custom driver selection logic and replaced it with built-in logic from `php-code-coverage`
 - The `RemoteXDebug` driver, it was a companion to an old Symfony bundle, not a generally-usable feature
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

[Unreleased]: https://github.com/dvdoug/behat-code-coverage/compare/v5.3.7..master

[5.3.7]: https://github.com/dvdoug/behat-code-coverage/compare/v5.3.6..v5.3.7
[5.3.6]: https://github.com/dvdoug/behat-code-coverage/compare/v5.3.5..v5.3.6
[5.3.5]: https://github.com/dvdoug/behat-code-coverage/compare/v5.3.4..v5.3.5
[5.3.4]: https://github.com/dvdoug/behat-code-coverage/compare/v5.3.3..v5.3.4
[5.3.3]: https://github.com/dvdoug/behat-code-coverage/compare/v5.3.2.1..v5.3.3
[5.3.2.1]: https://github.com/dvdoug/behat-code-coverage/compare/v5.3.2..v5.3.2.1
[5.3.2]: https://github.com/dvdoug/behat-code-coverage/compare/v5.3.1..v5.3.2
[5.3.1]: https://github.com/dvdoug/behat-code-coverage/compare/v5.3.0..v5.3.1
[5.3.0]: https://github.com/dvdoug/behat-code-coverage/compare/v5.2.2..v5.3.0
[5.2.2]: https://github.com/dvdoug/behat-code-coverage/compare/v5.2.1..v5.2.2
[5.2.1]: https://github.com/dvdoug/behat-code-coverage/compare/v5.2.0..v5.2.1
[5.2.0]: https://github.com/dvdoug/behat-code-coverage/compare/v5.1.1..v5.2.0
[5.1.1]: https://github.com/dvdoug/behat-code-coverage/compare/v5.1.0..v5.1.1
[5.1.0]: https://github.com/dvdoug/behat-code-coverage/compare/v5.0.1..v5.1.0
[5.0.1]: https://github.com/dvdoug/behat-code-coverage/compare/v5.0.0..v5.0.1
[5.0.0]: https://github.com/dvdoug/behat-code-coverage/compare/v4.1.1..v5.0.0
[4.1.1]: https://github.com/dvdoug/behat-code-coverage/compare/v4.1.0..v4.1.1
[4.1.0]: https://github.com/dvdoug/behat-code-coverage/compare/v4.0.1..v4.1.0
[4.0.1]: https://github.com/dvdoug/behat-code-coverage/compare/v4.0.0..v4.0.1
[4.0.0]: https://github.com/dvdoug/behat-code-coverage/compare/v3.4.1..v4.0.0
