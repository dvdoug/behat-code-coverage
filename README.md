behat-code-coverage
===================
[![Build Status](https://dev.azure.com/dvdoug/behat-code-coverage/_apis/build/status/dvdoug.behat-code-coverage?branchName=master)](https://dev.azure.com/dvdoug/behat-code-coverage/_build/latest?definitionId=1&branchName=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/dvdoug/behat-code-coverage/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/dvdoug/behat-code-coverage/?branch=master)
[![Download count](https://img.shields.io/packagist/dt/dvdoug/behat-code-coverage.svg)](https://packagist.org/packages/dvdoug/behat-code-coverage)
[![Current version](https://img.shields.io/packagist/v/dvdoug/behat-code-coverage.svg)](https://packagist.org/packages/dvdoug/behat-code-coverage)

The authors of [Behat][3] pedantically, but correctly, [point out][6] that `.feature` files are not strictly speaking
tests even though when constructed properly the scenarios described in them should cover both happy and sad paths in an
application. Behat is a _scenario_ runner, not a _test_ runner. The scenarios might be run by hand. Or the application
under scrutiny might not be a local PHP application, it might be running on a remote server and/or the software might
not even written in PHP. Additionally by the very nature of needing to invoke the entire application to perform each
scenario, it would be very hard to construct a set of scenarios that cover all possible codepaths in an application.
Something like PHPUnit is much better to use here if your goal is comprehensive code coverage as you can unit test each
component in isolation.

However, out in the real world we don't normally draw a distinction between the `.feature` files as a standalone concept
and the `Contexts` that implement them - we simply refer to _Behat testing_. We also tend to use Behat when the
application being tested is written in PHP. And as with any test suite, it's nice to know how much of your application
code is covered by a test suite. What you do with that information is up to you :)

behat-code-coverage is a Behat extension that can generate code coverage reports when testing a PHP application using
Behat.

## Requirements

- PHP 7.3+
- [Behat][3]
- [Xdebug][5] or [PCOV][6] extensions enabled, or running under phpdbg

## Change Log

Please see [CHANGELOG.md](CHANGELOG.md) for information on recent changes.

## Install

Install this package as a development dependency in your project:

    $ composer require --dev dvdoug/behat-code-coverage

Enable extension by editing `behat.yml` of your project:

``` yaml
default:
  extensions:
    DVDoug\Behat\CodeCoverage\Extension:  # or `LeanPHP\Behat\CodeCoverage\Extension` if you also need to work with older versions
      filter:
        whitelist:
          include:
            directories:
              'src': ~
      reports:
        html:
            target: build/coverage-behat
```

This will sufficient to enable Code Coverage generation in `html` format in
`build/coverage-behat` directory. This extension supports various
[Configuration options](#configuration-options). For a fully annotated example
configuration file check [Configuration section](#configuration).

## Usage

If you execute `vendor/bin/behat` command, you will see code coverage generated in
`target` (i.e. `build/coverage-behat`) directory (in `html` format):

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
      # filter options
      filter:
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

- `filter` - various filter options:
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
- `reports` - report options:
    - `clover`
        - `name` - Project name (optional)
        - `target` - Output filename
    - `crap4j`
        - `name`  - Project name (optional)
        - `target` - Output filename
    - `html`
        - `target` - Output directory
        - `lowUpperBound` - Max % coverage considered low (optional)
        - `highLowerBound` - Min % coverage considered high (optional)
    - `php`
        - `target` - Output filename
    - `text`
        - `showColors` - use colors (optional)
        - `showUncoveredFiles` - include files with 0% coverage in output (optional)
        - `showOnlySummary` - show only summary output (optional)
        - `lowUpperBound` - Max % coverage considered low (optional)
        - `highLowerBound` - Min % coverage considered high (optional)
    - `xml`
        - `target` - Output directory
## License + Acknowledgements
Licensed under [BSD-2-Clause License](LICENSE).

This extension was created as a fork of [leanphp/behat-code-coverage][0] (abandoned) which had previously been created as a fork of [vipsoft/code-coverage-extension][1] (abandoned).

[0]: https://github.com/leanphp/behat-code-coverage
[1]: https://github.com/vipsoft/code-coverage-extension
[2]: https://github.com/vipsoft/code-coverage-common
[3]: http://behat.org/
[4]: http://mink.behat.org
[5]: https://xdebug.org/
[6]: https://github.com/Behat/Behat/issues/92
[7]: https://github.com/krakjoe/pcov
