Behat Code Coverage
===================

Behat Code Coverage is an extension for `Behat`_ that can generate code coverage reports when testing a PHP application.
The extension wraps the same `php-code-coverage`_ library that is used by PHPUnit thereby producing
reports that are familiar to developers and interoperable with other tooling.

.. note::
    The authors of Behat pedantically, but correctly, `point out`_ that ``.feature`` files are not strictly speaking
    tests even though when constructed properly the scenarios described in them should cover both the happy and sad paths
    in an application.

    Technically, Behat is a *scenario* runner, not a *test* runner. The scenarios might be run by hand. Or the application
    under scrutiny might not be a local PHP application, it might be running on a remote server and/or the software might
    not even be written in PHP. Additionally by the very nature of needing to invoke the entire application to perform each
    scenario, it would be very hard to construct a set of scenarios that cover all possible codepaths in an application.
    Something like PHPUnit is much better to use here if your goal is comprehensive code coverage as you can unit test each
    component in isolation.

    However, out in the real world we don't normally draw a distinction between the ``.feature`` files as a standalone concept
    and the ``Context``\s that implement them - we simply refer to Behat testing. We also tend to use Behat when the
    application being tested is written in PHP. And as with any test suite, it's nice to know how much of your application
    code is covered by a test suite. What you do with that information is up to you :)

License
-------
Behat Code Coverage is licensed under the BSD-2-Clause License. See `license.txt`_ for full details.

.. _license.txt: https://github.com/dvdoug/behat-code-coverage/blob/master/license.txt

.. _Behat: https://docs.behat.org/en/latest/

.. _php-code-coverage: https://github.com/sebastianbergmann/php-code-coverage

.. _point out: https://github.com/Behat/Behat/issues/92


.. toctree::
    :maxdepth: 1

    installation
    configuration

.. toctree::
    :maxdepth: 1
    :caption: About

    changelog
