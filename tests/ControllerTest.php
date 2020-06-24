<?php

declare(strict_types=1);

namespace DVDoug\Behat\CodeCoverage\Test;

use DVDoug\Behat\CodeCoverage\Controller\Cli\CodeCoverageController;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;

class ControllerTest extends TestCase
{
    public function testSkipCoverageOptionCreated(): void
    {
        $command = new Command();
        $controller = new CodeCoverageController();
        $controller->configure($command);

        self::assertArrayHasKey('no-coverage', $command->getDefinition()->getOptions());
    }
}
