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
use SebastianBergmann\CodeCoverage\Report\Facade;
use SebastianBergmann\CodeCoverage\Report\Html\Colors;
use SebastianBergmann\CodeCoverage\Report\Html\CustomCssFile;
use SebastianBergmann\CodeCoverage\Report\Html\Facade as HtmlFacade;
use SebastianBergmann\CodeCoverage\Report\OpenClover;
use SebastianBergmann\CodeCoverage\Report\PHP;
use SebastianBergmann\CodeCoverage\Report\Text;
use SebastianBergmann\CodeCoverage\Report\Thresholds;
use SebastianBergmann\CodeCoverage\Report\Xml\Facade as XmlFacade;
use SebastianBergmann\CodeCoverage\Serialization\Serializer;

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
        if (InstalledVersions::satisfies(new \Composer\Semver\VersionParser(), 'phpunit/php-code-coverage', '<14')) {
            $this->generateReportPreV14($coverage);

            return;
        }

        $facade = Facade::fromObject($coverage);

        foreach ($this->config as $format => $config) {
            switch ($format) {
                case 'php':
                case 'serialized':
                    $report = new Serializer();
                    $report->serialize($config['target'], $coverage, $config['includeGitInformation']);
                    break;
                case 'openclover':
                    $facade->renderOpenClover($config['target'], $config['name']);
                    break;
                case 'clover':
                    $facade->renderClover($config['target'], $config['name']);
                    break;
                case 'crap4j':
                    $facade->renderCrap4j($config['target'], $config['threshold'], $config['name']);
                    break;
                case 'html':
                    $thresholds = Thresholds::from(
                        $config['lowUpperBound'],
                        $config['highLowerBound'],
                    );
                    $colors = Colors::from(
                        $config['colors']['successLow'],
                        $config['colors']['successLowDark'],
                        $config['colors']['successMedium'],
                        $config['colors']['successMediumDark'],
                        $config['colors']['successHigh'],
                        $config['colors']['successHighDark'],
                        $config['colors']['successBar'],
                        $config['colors']['successBarDark'],
                        $config['colors']['warning'],
                        $config['colors']['warningDark'],
                        $config['colors']['warningBar'],
                        $config['colors']['warningBarDark'],
                        $config['colors']['danger'],
                        $config['colors']['dangerDark'],
                        $config['colors']['dangerBar'],
                        $config['colors']['dangerBarDark'],
                        $config['colors']['breadcrumbs'],
                        $config['colors']['breadcrumbsDark'],
                    );
                    if ($config['customCSSFile']) {
                        $customCss = CustomCssFile::from($config['customCSSFile']);
                    } else {
                        $customCss = CustomCssFile::default();
                    }
                    $facade->renderHtml(
                        $config['target'],
                        sprintf(
                            ' and <a href="https://behat.cc">Behat Code Coverage %s</a>',
                            InstalledVersions::getPrettyVersion('dvdoug/behat-code-coverage')
                        ),
                        $colors,
                        $thresholds,
                        $customCss
                    );
                    break;
                case 'text':
                    $thresholds = Thresholds::from(
                        $config['lowUpperBound'],
                        $config['highLowerBound'],
                    );
                    echo $facade->renderText($config['target'], $thresholds, $config['showUncoveredFiles'], $config['showOnlySummary'], $config['showColors']);
                    break;
                case 'xml':
                    $facade->renderXml($config['target']);
                    break;
                case 'cobertura':
                    $facade->renderCobertura($config['target']);
                    break;
            }
        }
    }

    /**
     * Generate report.
     */
    private function generateReportPreV14(CodeCoverage $coverage): void
    {
        foreach ($this->config as $format => $config) {
            switch ($format) {
                case 'php':
                    $report = new PHP();
                    $report->process($coverage, $config['target']);
                    break;
                case 'openclover':
                    $report = new OpenClover();
                    $report->process($coverage, $config['target'], $config['name']);
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
