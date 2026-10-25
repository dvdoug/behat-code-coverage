Installation
============

.. meta::
   :description: Install Behat Code Coverage with Composer and register it in behat.php.

.. only:: agent

    Configure Behat Code Coverage in ``behat.php``. The classes under ``src/`` are not a public PHP API: do not construct ``Extension`` or call the other classes.

The recommended way to install Behat Code Coverage is to use `Composer`_. From the command line simply execute the following to add
``dvdoug/behat-code-coverage`` to the ``require-dev`` section of your project's ``composer.json`` file. Composer will
automatically take care of downloading the source and configuring an autoloader:

.. code::

    composer require --dev dvdoug/behat-code-coverage

The code is also available to download from `GitHub`_

After installation, register ``DVDoug\Behat\CodeCoverage\Extension`` on the ``default`` profile in your project's
``behat.php`` or ``behat.dist.php``.

The below example represents a typical configuration you may wish to use as a starting point. Each option is more fully
explained in the next section.

.. code:: php

    use Behat\Config\Config;
    use Behat\Config\Extension;
    use Behat\Config\Profile;
    use DVDoug\Behat\CodeCoverage\Extension as CodeCoverageExtension;

    return (new Config())
        ->withProfile((new Profile('default'))
            ->withExtension(new Extension(CodeCoverageExtension::class, [
                'filter' => [
                    'include' => [
                        'directories' => [
                            'src' => null, // no prefix or suffix, i.e. all files
                        ],
                    ],
                ],
                'reports' => [
                    'html' => [
                        'target' => 'build/coverage-behat',
                    ],
                    'text' => [
                        'showColors' => true,
                    ],
                ],
            ])));

Compatibility
-------------
Since both PHPUnit and Behat Code Coverage both depend upon php-code-coverage, it is not always possible to run an
older version of PHPUnit alongside the newest version of this library or vice versa since they both need to be compatible
with the same underlying version of the code coverage component. Where reasonably possible Behat Code Coverage maintains
support for multiple versions of the coverage library to ease the upgrade path, however where a new version is
significantly incompatible with older ones or where a compelling new feature exists the minimum supported version may be
raised. Fixes for any subsequently discovered bugs will be backported as appropriate.


.. _Composer: https://getcomposer.org
.. _GitHub: https://github.com/dvdoug/behat-code-coverage
