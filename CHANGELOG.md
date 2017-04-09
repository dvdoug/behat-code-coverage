# CHANGELOG

All notable changes to [leanphp/behat-code-coverage][0] package will be
documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [3.1.x-dev] - UNRELEASED

- Update PHP requirement to `>=5.6` (from `>=5.3.10`)
- Update `guzzlehttp/guzzle` from `~3.0` to `~4.0`.
- Update `phpunit/php-code-coverage` from `~2.2` to `~4.0||~5.0`.
- Add/implement missing tests for Xml and Crap4j reporters
- Mark `phpdbg` or `xdebug` specific tests so they are skipped automatically
  (using phpunit's @requires).

## [3.0.0] - 2017-04-08 (backported `3.0.x-dev` + patches)

- Fixed compatibility with Symfony `2.x` and `3.x` #2
- Fixed abandoned guzzle dependency #6
- Enabled Windows based CI testing (appveyor) to make sure extension is
  compatible with Windows OS #5
- Merged commits from `3.0.x-dev` (`master` branch) of
  `vipsoft/code-coverage-extension` (adds support for Behat `~3.0`)
- Update Travis CI to test against all supported versions of PHP (`5.3` to
  `7.1`).
- Enable windows based CI tests (appveyor).
- Added `symfony/expression-language` as a dependency for users using older
  versions of PHP and symonfy `2.x` / `3.x`.

## [2.5.5] - 2017-04-04 (backported v2.5.0.5, original release on 2014-02-10)

**Note!** This version is a direct backport of `2.5.0.5` of
[vipsoft/code-coverage-extension][1] package with updated namespaces to work
as [leanphp/behat-code-coverage][0]. It additionally includes code from
[vipsoft/code-coverage-common][2] package, so in case the package would
disappear, this extension would still work.

- PHP `>=5.3.10`
- Behat `~2.4`
- Symfony components `~2.2,<2.4`
- PHPUnit `~4.0` (updated from `3.7.*` in order to make tests pass)
- Removed `vipsoft/code-coverage-common` dependency (code is now included in
  the package,  will be refactored in the future)
- Updated `vfsStream` from `1.2.*` to `1.3.*` to fix failing/skipped test
- Updated versions of dependencies and code is tested to run with Behat `2.5`.

[3.0.x-dev]: https://github.com/leanphp/behat-code-coverage/compare/v2.5.5...master
[2.5.5]: https://github.com/leanphp/behat-code-coverage/releases/tag/v2.5.5

[0]: https://github.com/leanphp/behat-code-coverage
[1]: https://github.com/vipsoft/code-coverage-extension
[2]: https://github.com/vipsoft/code-coverage-common
