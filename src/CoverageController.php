<?php
/**
 * Code Coverage Controller for Behat
 *
 * @copyright 2018 Danny Lewis
 * @license BSD-2-Clause
 */

namespace LeanPHP\Behat\CodeCoverage;

use Behat\Testwork\Cli\Controller;
use LeanPHP\Behat\CodeCoverage\Service\ReportService;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class CoverageController implements Controller
{

    private $container;
    private $coverage;
    private $reportService;
    private $compilerPasses;

    public function __construct(ContainerBuilder $container, CodeCoverage $coverage, ReportService $reportService, $compilerPasses)
    {
        $this->container = $container;
        $this->coverage = $coverage;
        $this->reportService = $reportService;
        $this->compilerPasses = $compilerPasses;
    }

    /**
     * {@inheritdoc}
     */
    public function configure(Command $command)
    {
        $command->addOption('coverage', null, InputOption::VALUE_NONE, 'Generate coverage report(s)');
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {

        if($input->getOption('coverage')) {

            $this->container->get('event_dispatcher')->addSubscriber(new Listener\EventListener($this->coverage, $this->reportService));

            foreach ($this->compilerPasses as $pass) {
                $pass->process($this->container);
            }


        }else{

            $this->container->get('event_dispatcher')->addSubscriber(new Listener\NoCoverage($this->container->get('cli.output')));

        }

    }

}
