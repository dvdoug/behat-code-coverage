<?php
/**
 * Behat Code Coverage
 */
declare(strict_types=1);

use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Filter;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->bind('$reportConfig', '%behat.code_coverage.config.reports%')
        ->autowire()
        ->autoconfigure()
        ->private();

    $services->load('DVDoug\\Behat\\CodeCoverage\\', __DIR__ . '/../src');

    // Register underlying services used from dependencies
    $services->set(CodeCoverage::class);
    $services->set(Filter::class);
};
