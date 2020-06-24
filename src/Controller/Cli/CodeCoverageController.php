<?php

declare(strict_types=1);
/**
 * @author ek9 <dev@ek9.co>
 *
 * @license BSD-2-Clause
 *
 * For the full copyright and license information, please see the LICENSE file
 * that was distributed with this source code.
 */

namespace DVDoug\Behat\CodeCoverage\Controller\Cli;

use Behat\Testwork\Cli\Controller;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Code Coverage Cli Controller.
 *
 * @author Danny Lewis
 */
class CodeCoverageController implements Controller
{
    /**
     * {@inheritdoc}
     */
    public function configure(Command $command): void
    {
        $command->addOption('no-coverage', null, InputOption::VALUE_NONE, 'Skip Code Coverage generation');
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function execute(InputInterface $input, OutputInterface $output): void
    {
    }
}
