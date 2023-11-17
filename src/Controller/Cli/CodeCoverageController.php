<?php
/**
 * Behat Code Coverage
 */
declare(strict_types=1);

namespace DVDoug\Behat\CodeCoverage\Controller\Cli;

use Behat\Testwork\Cli\Controller;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CodeCoverageController implements Controller
{
    public function configure(Command $command): void
    {
        $command->addOption('no-coverage', null, InputOption::VALUE_NONE, 'Skip Code Coverage generation');
    }

    /**
     * @codeCoverageIgnore
     */
    public function execute(InputInterface $input, OutputInterface $output): void
    {
    }
}
