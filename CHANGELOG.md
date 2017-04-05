# CHANGELOG

All notable changes to [leanphp/behat-code-coverage][0] package will be
documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

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

[2.5.5]: https://github.com/leanphp/behat-code-coverage/releases/tag/v2.5.5

[0]: https://github.com/leanphp/behat-code-coverage
[1]: https://github.com/vipsoft/code-coverage-extension
[2]: https://github.com/vipsoft/code-coverage-common
