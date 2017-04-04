behat-code-coverage
===================
[![Latest Stable Version](https://poser.pugx.org/leanphp/behat-code-coverage/v/stable)](https://packagist.org/packages/leanphp/behat-code-coverage)
[![Build Status][travis-image]][travis-url]
[![Latest Unstable Version](https://poser.pugx.org/leanphp/behat-code-coverage/v/unstable)](https://packagist.org/packages/leanphp/behat-code-coverage)
[![MIT License](https://poser.pugx.org/leanphp/behat-code-coverage/license)](https://packagist.org/packages/leanphp/behat-code-coverage)

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
    LeanPHP\BehathpSpec\CodeCoverage\CodeCoverageExtension:
      auth:       ~
      #create:
        #method:   POST
        #path:     /
      #read:
        #method:   GET
        #path:     /
      #delete:
        #method:   DELETE
        #path:     /
      drivers:
        #- remote
        - local
      filter:     ~
      report:
        format:   html
        options:
          target: build/behat-coverage
```

This will sufficient to enable Code Coverage generation and provideby using defaults
provided by the extension. This extension supports various [configuration
options](#Configuration Options). For a fully annotated example configuration
file check [Configuration section](#Configuration).

## Usage

If you execute `phpspec run` command, you will see code coverage generated in `coverage` directory (in `html` format):

    $ bin/phpspec run

## Configuration

You can see fully annotated `phpspec.yml` example file below, which can be used
as a starting point to further customize the defaults of the extension. The
configuration file below has all of the [Configuration Options](#Configuration
Options).

```yaml
# phpspec.yml
# ...
extensions:
  # ... other extensions ...
  # leanphp/phpspec-code-coverage
  LeanPHP\PhpSpec\CodeCoverage\CodeCoverageExtension:
    # Specify a list of formats in which code coverage report should be
    # generated.
    # Default: [html]
    format:
      - text
      - html
      #- clover
      #- php
    #
    # Specify output file/directory where code coverage report will be
    # generated. You can configure different output file/directory per
    # enabled format.
    # Default: coverage
    output:
      html: coverage
      #clover: coverage.xml
      #php: coverage.php
    #
    # Should uncovered files be included in the reports?
    # Default: true
    #show_uncovered_files: true
    #
    # Set lower upper bound for code coverage
    # Default: 35
    #lower_upper_bound: 35
    #
    # Set high lower bound for code coverage
    # Default: 70
    #high_lower_bound: 70
    #
    # Whilelist directories for which code generation should be done
    # Default: [src, lib]
    #
    whitelist:
      - src
      - lib
    #
    # Whiltelist files for which code generation should be done
    # Default: empty
    #whilelist_files:
      #- app/bootstrap.php
      #- web/index.php
    #
    # Blacklist directories for which code generation should NOT be done
    #blacklist:
      #- src/legacy
    #
    # Blacklist files for which code generation should NOT be done
    #blacklist_files:
      #- lib/bootstrap.php
```

### Configuration Options

* `format` (optional) a list of formats in which code coverage should be
  generated. Can be one or many of: `clover`, `php`, `text`, `html` (default
  `html`)
  **Note**: When using `clover` format option, you have to configure specific
  `output` file for the `clover` format (see below).
* `output` (optional) sets an output file/directory where specific code
  coverage format will be generated. If you configure multiple formats, takes
  a hash of `format:output` (e.g. `clover:coverage.xml`) (default `coverage`)
* `show_uncovered_files` (optional) for including uncovered files in coverage
  reports (default `true`)
* `lower_upper_bound` (optional) sets lower upper bound for code coverage
  (default `35`).
* `high_lower_bound` (optional) sets high lower bound for code coverage
  (default `70`)
* `whitelist` takes an array of directories to whitelist (default: `lib`,
  `src`).
* `whitelist_files` takes an array of files to whitelist (default: none).
* `blacklist` takes an array of directories to blacklist (default: `test,
  vendor, spec`)
* `blacklist_files` takes an array of files to blacklist

#
## Authors

Copyright (c) 2017 ek9 <dev@ek9.co> (https://ek9.co).

Copyright (c) 2013-2016 Anthon Pang, Konstantin Kudryashov
[everzet](http://github.com/everzet),
[Contributors](https://github.com/leanphp/behat-code-coverage/graphs/contributors)
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

