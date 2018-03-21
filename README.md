behat-code-coverage
===================
[![License](https://img.shields.io/packagist/l/leanphp/behat-code-coverage.svg?style=flat-square)](#License)
[![Latest Stable Version](https://img.shields.io/packagist/v/leanphp/behat-code-coverage.svg?style=flat-square)](https://packagist.org/packages/leanphp/behat-code-coverage)
[![Total Downloads](https://img.shields.io/packagist/dt/leanphp/behat-code-coverage.svg?style=flat-square)](https://packagist.org/packages/leanphp/behat-code-coverage)
[![Travis](https://img.shields.io/travis/leanphp/behat-code-coverage.svg?style=flat-square)](https://travis-ci.org/leanphp/behat-code-coverage)
[![AppVeyor](https://img.shields.io/appveyor/ci/leanphp/behat-code-coverage/master.svg?style=flat-square)](https://ci.appveyor.com/project/leanphp/behat-code-coverage)
[![Pre Release](https://img.shields.io/packagist/vpre/leanphp/behat-code-coverage.svg?style=flat-square)](https://packagist.org/packages/leanphp/behat-code-coverage)

[behat-code-coverage][0] is a [Behat][3] extension that generates Code
Coverage reports for [Behat][3] tests.

Generating Code Coverage reports allows you to to analyze which parts of your
codebase are tested and how well. However, Code Coverage alone should NOT be
used as a single metric defining how good your tests are.

**Note!** This is a maintained fork of [vipsoft/code-coverage-extension][1],
including codebase for [vipsoft/code-coverage-common][2] package with
compatible version numbers for stable releases.

## Requirements

- PHP 5.6+ / 7.0+
- [Behat v3][3]
- [Xdebug][5] or [phpdbg][6] extension enabled

## Change Log

Please see [CHANGELOG.md](CHANGELOG.md) for information on recent changes.

## Install

Install this package as a development dependency in your project:

    $ composer require --dev leanphp/behat-code-coverage

Enable extension by editing `behat.yml` of your project:

``` yaml
default:
  extensions:
    LeanPHP\Behat\CodeCoverage\Extension:
      auth:       ~
      drivers:
        - local
      filter:     ~
      report:
        format:   html
        options:
          target: build/behat-coverage
```

This will sufficient to enable Code Coverage generation in `html` format in
`build/behat-coverage` directory. This extension supports various
[Configuration options](#configuration-options). For a fully annotated example
configuration file check [Configuration section](#configuration).

## Usage

If you execute `bin/behat` command, you will see code coverage generated in
`target` (i.e. `build/behat-coverage`) directory (in `html` format):

    $ bin/behat

### Running with phpdbg

This extension now supports [phpdbg][6], which results in faster execution when
using more recent versions of PHP. Run `phpspec` via [phpdbg][6]:

    $ phpdbg -qrr bin/behat run

## Configuration

You can see fully annotated `behat.yml` example file below, which can be used
as a starting point to further customize the defaults of the extension. The
configuration file below has all of the [Configuration Options](#Configuration
Options).

```yaml
# behat.yml
# ...
default:
  extensions:
    LeanPHP\Behat\CodeCoverage\Extension:
      # http auth (optional)
      auth:        ~
      # select which driver to use when gatherig coverage data
      drivers:
        - local     # local Xdebug driver
      # filter options
      filter:
        forceCoversAnnotation:                false
        mapTestClassNameToCoveredClassName:   false
        whitelist:
          addUncoveredFilesFromWhitelist:     true
          processUncoveredFilesFromWhitelist: false
          include:
            directories:
              'src': ~
              'tests':
                suffix: '.php'
#           files:
#             - script1.php
#             - script2.php
#         exclude:
#           directories:
#             'vendor': ~
#             'path/to/dir':
#               'suffix': '.php'
#               'prefix': 'Test'
#           files:
#             - tests/bootstrap.php
      # report configuration
      report:
        # report format (html, clover, php, text)
        format:    html
        # report options
        options:
          target: build/behat-coverage/html
```

### Configuration Options

* `auth` - HTTP authentication options (optional).
- `create` (`method` / `path`) - override options for create method:
    - `method` - specify a method (default: `POST`)
    - `path` - specify path (default: `/`)
- `read` (`method` / `path`) - override options (method and path) for read
  method.
    - `method` - specify a method (default: `GET`)
    - `path` - specify path (default: `/`)
- `delete` (`method` / `path`) - override options (method and path) for delete
  method.
    - `method` - specify a method (default: `DELETE`)
    - `path` - specify path (default: `/`)
- `drivers` - a list of drivers for gathering code coverage data:
    - `local` - local Xdebug driver (default).
    - `remote` - remote Xdebug driver (disabled by default).
- `filter` - various filter options:
    - `forceCoversAnnotation` - (default: `false`)
    - `mapTestClassNameToCoveredClassName` - (default: `false`)
    - `whiltelist` - whitelist specific options:
        - `addUncoveredFilesFromWhiltelist` - (default: `true`)
        - `processUncoveredFilesFromWhitelist` - (default: `false`)
        - `include` - a list of files or directories to include in whitelist:
            - `directories` - key containing whitelisted directories to include.
                - `suffix` - suffix for files to be included (default: `'.php'`)
                - `prefix` - prefix of files to be included (default: `''`)
                  (optional)
            - `files` - a list containing whitelisted files to include.
        - `exclude` - a list of files or directories to exclude from whitelist:
            - `directories` - key containing whitelisted directories to exclude.
                - `suffix` - suffix for files to be included (default: `'.php'`)
                - `prefix` - prefix of files to be included (default: `''`)
                  (optional)
            - `files` - key containing whitelisted files to exclude.
- `report` - reporter options:
    - `format` - specify report format (`html`, `clover`, `php`, `text`)
    - `options` - format options:
        - `target` - target/output directory

## Authors

Copyright (c) 2017 ek9 <dev@ek9.co> (https://ek9.co).

Copyright (c) 2013-2016 Anthon Pang, Konstantin Kudryashov
[everzet](http://github.com/everzet) and [various
contributors](https://github.com/leanphp/behat-code-coverage/graphs/contributors)
for portions of code from [vipsoft/code-coverage-extension][1] and
[vipsoft/code-coverage-common][2] projects.

## License

Licensed under [BSD-2-Clause License](LICENSE).

[0]: https://github.com/leanphp/behat-code-coverage
[1]: https://github.com/vipsoft/code-coverage-extension
[2]: https://github.com/vipsoft/code-coverage-common
[3]: http://behat.org/en/v2.5/
[4]: http://mink.behat.org
[5]: https://xdebug.org/
[6]: http://phpdbg.com/

[travis-image]: https://travis-ci.org/leanphp/behat-code-coverage.svg
[travis-url]: https://travis-ci.org/leanphp/behat-code-coverage

