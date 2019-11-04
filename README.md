behat-code-coverage
===================
[![Build Status](https://dev.azure.com/dvdoug/behat-code-coverage/_apis/build/status/dvdoug.behat-code-coverage?branchName=master)](https://dev.azure.com/dvdoug/behat-code-coverage/_build/latest?definitionId=1&branchName=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/dvdoug/behat-code-coverage/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/dvdoug/behat-code-coverage/?branch=master)
[![Download count](https://img.shields.io/packagist/dt/dvdoug/behat-code-coverage.svg)](https://packagist.org/packages/dvdoug/behat-code-coverage)
[![Current version](https://img.shields.io/packagist/v/dvdoug/behat-code-coverage.svg)](https://packagist.org/packages/dvdoug/behat-code-coverage)

[behat-code-coverage][0] is a [Behat][3] extension that generates Code
Coverage reports for [Behat][3] tests.

Generating Code Coverage reports allows you to to analyze which parts of your
codebase are tested and how well. However, Code Coverage alone should NOT be
used as a single metric defining how good your tests are.

**Note!** This is a maintained fork of [leanphp/behat-code-coverage][1],
with compatible version numbers for stable releases.

## Requirements

- PHP 7.1+
- [Behat v3][3]
- [Xdebug][5] or phpdbg extension enabled

## Change Log

Please see [CHANGELOG.md](CHANGELOG.md) for information on recent changes.

## Install

Install this package as a development dependency in your project:

    $ composer require --dev dvdoug/behat-code-coverage

Enable extension by editing `behat.yml` of your project:

``` yaml
default:
  extensions:
    DVDoug\Behat\CodeCoverage\Extension:
      drivers:
        - local
      filter:
        whitelist:
          include:
            directories:
              'src': ~
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

If you execute `vendor/bin/behat` command, you will see code coverage generated in
`target` (i.e. `build/behat-coverage`) directory (in `html` format):

    $ vendor/bin/behat

### Running with phpdbg

This extension now supports `phpdbg`, which results in faster execution when
using more recent versions of PHP. Run `behat` via `phpdbg`:

    $ phpdbg -qrr vendor/bin/behat

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
    DVDoug\Behat\CodeCoverage\Extension:
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
      # report configuration. For a report to be generated, include at least 1 configuration option under the relevant key
      reports:
        clover:
          name: 'Project name'
          target: build/coverage-behat/clover.xml
        crap4j:
          name: 'Project name'
          target: build/coverage-behat/crap4j.xml
        html:
          target: build/coverage-behat
          lowUpperBound: 50
          highLowerBound: 90
        php:
          target: build/coverage-behat/coverage.php
        text:
          showColors: true
          showUncoveredFiles: true
          showOnlySummary: false
          lowUpperBound: 50
          highLowerBound: 90
        xml:
          target: build/coverage-behat
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

## License

Licensed under [BSD-2-Clause License](LICENSE).

[0]: https://github.com/leanphp/behat-code-coverage
[1]: https://github.com/vipsoft/code-coverage-extension
[2]: https://github.com/vipsoft/code-coverage-common
[3]: http://behat.org/en/v2.5/
[4]: http://mink.behat.org
[5]: https://xdebug.org/
