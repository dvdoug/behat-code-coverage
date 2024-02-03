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
use SebastianBergmann\CodeCoverage\Report\Html\Colors;
use SebastianBergmann\CodeCoverage\Report\Html\CustomCssFile;
use SebastianBergmann\CodeCoverage\Report\Html\Facade as HtmlFacade;
use SebastianBergmann\CodeCoverage\Report\PHP;
use SebastianBergmann\CodeCoverage\Report\Text;
use SebastianBergmann\CodeCoverage\Report\Thresholds;
use SebastianBergmann\CodeCoverage\Report\Xml\Facade as XmlFacade;

use function sprintf;

class ReportService
{
    private array $config;

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
                case 'php':
                    $report = new PHP();
                    $report->process($coverage, $config['target']);
                    break;
                case 'clover':
                    $report = new Clover();
                    $report->process($coverage, $config['target'], $config['name']);
                    break;
                case 'crap4j':
                    $report = new Crap4j();
                    $report->process($coverage, $config['target'], $config['name']);
                    break;
                case 'html':
                    $thresholds = Thresholds::from(
                        $config['lowUpperBound'],
                        $config['highLowerBound'],
                    );
                    $colors = Colors::from(
                        $config['colors']['successLow'],
                        $config['colors']['successMedium'],
                        $config['colors']['successHigh'],
                        $config['colors']['warning'],
                        $config['colors']['danger'],
                    );
                    if ($config['customCSSFile']) {
                        $customCss = CustomCssFile::from($config['customCSSFile']);
                    } else {
                        $customCss = CustomCssFile::default();
                    }
                    $report = new HtmlFacade(
                        sprintf(
                            ' and <a href="https://behat.cc">Behat Code Coverage %s</a>',
                            InstalledVersions::getPrettyVersion('dvdoug/behat-code-coverage')
                        ),
                        $colors,
                        $thresholds,
                        $customCss
                    );
                    $report->process($coverage, $config['target']);
                    break;
                case 'text':
                    $thresholds = Thresholds::from(
                        $config['lowUpperBound'],
                        $config['highLowerBound'],
                    );
                    $report = new Text(
                        $thresholds,
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
