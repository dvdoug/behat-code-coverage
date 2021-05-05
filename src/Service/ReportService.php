<?php
/**
 * Behat Code Coverage
 */
declare(strict_types=1);

namespace DVDoug\Behat\CodeCoverage\Service;

use Composer\InstalledVersions;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Report\Clover;
use SebastianBergmann\CodeCoverage\Report\Cobertura;
use SebastianBergmann\CodeCoverage\Report\Crap4j;
use SebastianBergmann\CodeCoverage\Report\Html\Facade as HtmlFacade;
use SebastianBergmann\CodeCoverage\Report\PHP;
use SebastianBergmann\CodeCoverage\Report\Text;
use SebastianBergmann\CodeCoverage\Report\Xml\Facade as XmlFacade;
use function sprintf;

class ReportService
{
    /**
     * @var array
     */
    private $config;

    /**
     * Constructor.
     */
    public function __construct(array $reportConfig)
    {
        $this->config = $reportConfig;
    }

    /**
     * Generate report.
     */
    public function generateReport(CodeCoverage $coverage): void
    {
        foreach ($this->config as $format => $config) {
            switch ($format) {
                case 'clover':
                    $report = new Clover();
                    $report->process($coverage, $config['target'], $config['name']);
                    break;
                case 'crap4j':
                    $report = new Crap4j();
                    $report->process($coverage, $config['target'], $config['name']);
                    break;
                case 'html':
                    $report = new HtmlFacade(
                        $config['lowUpperBound'],
                        $config['highLowerBound'],
                        sprintf(' and <a href="https://behat.cc">Behat Code Coverage %s</a>',
                            InstalledVersions::getPrettyVersion('dvdoug/behat-code-coverage')
                        ));
                    $report->process($coverage, $config['target']);
                    break;
                case 'php':
                    $report = new PHP();
                    $report->process($coverage, $config['target']);
                    break;
                case 'text':
                    $report = new Text(
                        $config['lowUpperBound'],
                        $config['highLowerBound'],
                        $config['showUncoveredFiles'],
                        $config['showOnlySummary']
                    );
                    echo $report->process($coverage, $config['showColors']);
                    break;
                case 'xml':
                    $report = new XmlFacade('');
                    $report->process($coverage, $config['target']);
                    break;
                case 'cobertura':
                    $report = new Cobertura();
                    $report->process($coverage, $config['target']);
                    break;
            }
        }
    }
}
