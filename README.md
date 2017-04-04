behat-code-coverage
===================
[![Latest Stable Version](https://poser.pugx.org/leanphp/behat-code-coverage/v/stable)](https://packagist.org/packages/leanphp/behat-code-coverage)
[![Build Status][travis-image]][travis-url]
[![Latest Unstable Version](https://poser.pugx.org/leanphp/behat-code-coverage/v/unstable)](https://packagist.org/packages/leanphp/behat-code-coverage)
[![BSD-2-Clause License](https://poser.pugx.org/leanphp/behat-code-coverage/license)](https://packagist.org/packages/leanphp/behat-code-coverage)

[behat-code-coverage][0] is a [Behat][3] extension that generates Code
Coverage reports for [Behat][3] tests.

Generating Code Coverage reports allows you to to analyze which parts of your
codebase are tested and how well. However, Code Coverage alone should NOT be
used as a single metric defining how good your tests are.

**Note!** This is a maintained fork of [vipsoft/code-coverage-extension][1],
including codebase for [vipsoft/code-coverage-common][2] package with
compatible version numbers for stable releases.

## Requirements

- PHP 5.3.10+
- [Behat v2.4+][3]
- [Mink 1.4+][4]
- [Xdebug][5] extension enabled in PHP

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
[configuration options](#Configuration Options). For a fully annotated example
configuration file check [Configuration section](#Configuration).

## Usage

If you execute `bin/behat` command, you will see code coverage generated in
`target` (i.e. `build/behat-coverage`) directory (in `html` format):

    $ bin/behat

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
              'src':
                prefix: 'src'
              'tests':
                prefix: 'src'
        blacklist:
          include:
            directories:
              'vendor':
                prefix: 'vendor'
      # report configuration
      report:
        # report format (html, clover, php, text)
        format:    html
        # report options
        options:
          target:: build/behat-coverage/html
```

### Configuration Options

* `auth` - HTTP authentication options (optional).
- `create` (`method` / `path`) - *TBA*.
- `read` (`method` / `path`) - *TBA*.
- `delete` (`method` / `path`) - *TBA*.
- `drivers` - a list of drivers for gathering code coverage data:
    - `remote` - remote Xdebug driver.
    - `local` - local Xdebug driver. 
- `filter` - various filter options:
    - `forceCoversAnnotation` - *TBA*
    - `mapTestClassNameToCoveredClassName` - *TBA*
    - `whiltelist` - whitelist specific options:
        - `addUncoveredFilesFromWhiltelist` - *TBA*
        - `processUncoveredFilesFromWhitelist` - *TBA*
        - `include` - a list of files or directories to include in whitelist:
            - `directories` - key containing whitelisted directories to include.
            - `files` - key containing whitelisted files to include.
        - `exclude` - a list of files or directories to exclude from whitelist:
            - `directories` - key containing whitelisted directories to exclude.
            - `files` - key containing whitelisted files to exclude.
    - `blacklist` - blacklist specific options:
        - `include` - a list of files or directories to include in blacklist:
            - `directories` - key containing blacklisted directories to include.
            - `files` - key containing blacklisted files to include.
        - `exclude` - a list of files or directories to exclude from blacklist:
            - `directories` - key containing blacklisted directories to exclude.
            - `files` - key containing blacklisted files to exclude.
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

[travis-image]: https://travis-ci.org/leanphp/behat-code-coverage.svg
[travis-url]: https://travis-ci.org/leanphp/behat-code-coverage

