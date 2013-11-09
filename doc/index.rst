=====================
CodeCoverageExtension
=====================

The Code Coverage extension allows you to collect local and/or remote code
coverage.

Use this extension to find unused/obsolete contexts or step definitions.

Please don't use this extension to achieve 100% code coverage.

Installation
============
This extension requires:

* Behat 2.4+
* Mink 1.4+

Through Composer
----------------
1. Set dependencies in your **composer.json**:

.. code-block:: js

    {
        "require": {
            ...
            "vipsoft/code-coverage-extension": "*"
        }
    }

2. Install/update your vendors:

.. code-block:: bash

    $ curl http://getcomposer.org/installer | php
    $ php composer.phar install

Through PHAR
------------
Download the .phar archive:

* `code_coverage_extension.phar <http://behat.org/downloads/code_coverage_extension.phar>`_

Configuration
=============
Activate extension in your **behat.yml** and define your routes for remote code coverage collection (relative to Mink's base URL):

.. code-block:: yaml

    # behat.yml
    default:
      # ...
      extensions:
        VIPSoft\CodeCoverageExtension\Extension:
          auth:       ~
          create:
            method:   POST
            path:     /
          read:
            method:   GET
            path:     /
          delete:
            method:   DELETE
            path:     /
          drivers:
            - remote
            - local
          output_directory: /tmp/report

Settings
--------
If HTTP Authentication is required, use:

.. code-block:: yaml

    # behat.yml
    default:
      # ...
      extensions:
        VIPSoft\CodeCoverageExtension\Extension:
          auth:
            user:     your_user
            password: your_password
          ...

There are two code coverage drivers.  The "local" driver will collect code
coverage from the PHP instance running Behat.  The "remote" driver will
collect code coverage for the Symfony 2 application under test on a remote
web server.

The "output_directory" determines where the extension will write the code
coverage report.

Limitations
-----------
Web server clusters not supported (because the Code Coverage bundle uses a
SQLite database).  So, not compatible with distributed testing environments
either (e.g., use Behat GearmanExtension).

No filtering yet (but it is supported by PHP_CodeCoverage_Report_HTML).

Source
======
`Github <https://github.com/vipsoft/code-coverage-extension>`_

Copyright
=========
Copyright (c) 2013 Anthon Pang.  See **LICENSE** for details.

Contributors
============
* Anthon Pang `(robocoder) <http://github.com/robocoder>`_
* `Others <https://github.com/vipsoft/code-coverage-extension/graphs/contributors>`_
