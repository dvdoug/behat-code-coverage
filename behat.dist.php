<?php

use Behat\Config\Config;
use Behat\Config\Extension;
use Behat\Config\Profile;
use Behat\Config\Suite;
use DVDoug\Behat\CodeCoverage\Extension as CodeCoverageExtension;

return (new Config())
    ->withProfile((new Profile('default'))
        ->withExtension(new Extension(CodeCoverageExtension::class, [
            'cache' => 'build/behat-code-coverage-cache',
            'filter' => [
                'include' => [
                    'directories' => [
                        'src' => null,
                    ],
                ],
                'includeUncoveredFiles' => true,
            ],
            'reports' => [
                'cobertura' => [
                    'target' => 'build/coverage-behat/cobertura.xml',
                ],
                'clover' => [
                    'target' => 'build/coverage-behat/clover.xml',
                ],
                'crap4j' => [
                    'target' => 'build/coverage-behat/crap4j.xml',
                ],
                'html' => [
                    'target' => 'build/coverage-behat',
                ],
                'openclover' => [
                    'target' => 'build/coverage-behat/openclover.xml',
                ],
                'php' => [
                    'target' => 'build/coverage-behat/coverage.cov',
                ],
                'text' => [
                    'showColors' => true,
                    'showUncoveredFiles' => true,
                ],
                'xml' => [
                    'target' => 'build/coverage-behat',
                ],
            ],
        ]))
        ->withSuite((new Suite('maths'))
            ->withContexts('MathsContext')
            ->withPaths('%paths.base%/features/maths')));
