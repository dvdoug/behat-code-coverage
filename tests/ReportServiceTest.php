<?php
/**
 * Behat Code Coverage
 */
declare(strict_types=1);

namespace DVDoug\Behat\CodeCoverage\Test;

use DVDoug\Behat\CodeCoverage\Service\ReportService;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Driver\Driver;
use SebastianBergmann\CodeCoverage\Filter;
use Symfony\Component\Filesystem\Filesystem;

use function file_get_contents;
use function ob_get_clean;
use function ob_start;
use function sys_get_temp_dir;

class ReportServiceTest extends TestCase
{
    public function testCanGenerateTextReport(): void
    {
        $driver = $this->createStub(Driver::class);
        $coverage = new CodeCoverage($driver, new Filter());

        $reportService = new ReportService(
            [
                'text' => [
                    'target' => null,
                    'lowUpperBound' => 50,
                    'highLowerBound' => 90,
                    'showColors' => true,
                    'showUncoveredFiles' => true,
                    'showOnlySummary' => true,
                ],
            ]
        );

        ob_start();
        $reportService->generateReport($coverage);
        $report = ob_get_clean();

        self::assertNotEmpty($report);
    }

    public function testCanGenerateCloverReport(): void
    {
        $filesystem = new Filesystem();
        $reportFilename = sys_get_temp_dir() . '/clover.xml';
        $filesystem->remove($reportFilename);

        $driver = $this->createStub(Driver::class);
        $coverage = new CodeCoverage($driver, new Filter());

        $reportService = new ReportService(
            [
                'clover' => [
                    'target' => $reportFilename,
                    'name' => 'SomeName',
                ],
            ]
        );

        $reportService->generateReport($coverage);
        $report = file_get_contents($reportFilename);

        self::assertNotEmpty($report);

        $filesystem->remove($reportFilename);
    }

    public function testCanGenerateOpenCloverReport(): void
    {
        $filesystem = new Filesystem();
        $reportFilename = sys_get_temp_dir() . '/openclover.xml';
        $filesystem->remove($reportFilename);

        $driver = $this->createStub(Driver::class);
        $coverage = new CodeCoverage($driver, new Filter());

        $reportService = new ReportService(
            [
                'openclover' => [
                    'target' => $reportFilename,
                    'name' => 'SomeName',
                ],
            ]
        );

        $reportService->generateReport($coverage);
        $report = file_get_contents($reportFilename);

        self::assertNotEmpty($report);

        $filesystem->remove($reportFilename);
    }

    public function testCanGenerateCrap4jReport(): void
    {
        $filesystem = new Filesystem();
        $reportFilename = sys_get_temp_dir() . '/crap4j.xml';
        $filesystem->remove($reportFilename);

        $driver = $this->createStub(Driver::class);
        $coverage = new CodeCoverage($driver, new Filter());

        $reportService = new ReportService(
            [
                'crap4j' => [
                    'target' => $reportFilename,
                    'name' => 'SomeName',
                    'threshold' => 30,
                ],
            ]
        );

        $reportService->generateReport($coverage);
        $report = file_get_contents($reportFilename);

        self::assertNotEmpty($report);

        $filesystem->remove($reportFilename);
    }

    public function testCanGeneratePHPReport(): void
    {
        $filesystem = new Filesystem();
        $reportFilename = sys_get_temp_dir() . '/report.php';
        $filesystem->remove($reportFilename);

        $driver = $this->createStub(Driver::class);
        $coverage = new CodeCoverage($driver, new Filter());

        $reportService = new ReportService(
            [
                'php' => [
                    'target' => $reportFilename,
                    'includeGitInformation' => false,
                ],
            ]
        );

        $reportService->generateReport($coverage);
        $report = file_get_contents($reportFilename);

        self::assertNotEmpty($report);

        $filesystem->remove($reportFilename);
    }

    public function testCanGenerateHTMLReport(): void
    {
        $filesystem = new Filesystem();
        $reportDirectory = sys_get_temp_dir() . '/report-html';
        $filesystem->remove($reportDirectory);

        $driver = $this->createStub(Driver::class);
        $coverage = new CodeCoverage($driver, new Filter());

        $reportService = new ReportService(
            [
                'html' => [
                    'target' => $reportDirectory,
                    'lowUpperBound' => 50,
                    'highLowerBound' => 90,
                    'colors' => [
                        'successLow' => '#d6e6f2',
                        'successLowDark' => '#1e3550',
                        'successMedium' => '#b3d1e8',
                        'successMediumDark' => '#2d4f6e',
                        'successHigh' => '#8cb4d5',
                        'successHighDark' => '#2a4a6b',
                        'successBar' => '#1a73b4',
                        'successBarDark' => '#1560a0',
                        'warning' => '#fdf0d5',
                        'warningDark' => '#3d3010',
                        'warningBar' => '#e5a100',
                        'warningBarDark' => '#b88a00',
                        'danger' => '#fad4c0',
                        'dangerDark' => '#4a2a10',
                        'dangerBar' => '#d45500',
                        'dangerBarDark' => '#b54400',
                        'breadcrumbs' => 'var(--bs-gray-200)',
                        'breadcrumbsDark' => 'var(--bs-gray-800)',
                    ],
                    'customCSSFile' => null,
                ],
            ]
        );

        $reportService->generateReport($coverage);
        self::assertFileExists($reportDirectory . '/index.html');

        $filesystem->remove($reportDirectory);
    }

    public function testCanGenerateXMLReport(): void
    {
        $filesystem = new Filesystem();
        $reportDirectory = sys_get_temp_dir() . '/report-xml';
        $filesystem->remove($reportDirectory);

        $driver = $this->createStub(Driver::class);
        $coverage = new CodeCoverage($driver, new Filter());

        $reportService = new ReportService(
            [
                'xml' => [
                    'target' => $reportDirectory,
                ],
            ]
        );

        $reportService->generateReport($coverage);
        self::assertFileExists($reportDirectory . '/index.xml');

        $filesystem->remove($reportDirectory);
    }

    public function testCanGenerateCoberturaReport(): void
    {
        $filesystem = new Filesystem();
        $reportFilename = sys_get_temp_dir() . '/cobertura.xml';
        $filesystem->remove($reportFilename);

        $driver = $this->createStub(Driver::class);
        $coverage = new CodeCoverage($driver, new Filter());

        $reportService = new ReportService(
            [
                'cobertura' => [
                    'target' => $reportFilename,
                ],
            ]
        );

        $reportService->generateReport($coverage);
        $report = file_get_contents($reportFilename);

        self::assertNotEmpty($report);

        $filesystem->remove($reportFilename);
    }

    public function testCanGenerateMultipleReport(): void
    {
        $filesystem = new Filesystem();
        $cloverReportFilename = sys_get_temp_dir() . '/clover.xml';
        $crap4jReportFilename = sys_get_temp_dir() . '/crap4j.xml';
        $filesystem->remove($cloverReportFilename);
        $filesystem->remove($crap4jReportFilename);

        $driver = $this->createStub(Driver::class);
        $coverage = new CodeCoverage($driver, new Filter());

        $reportService = new ReportService(
            [
                'clover' => [
                    'target' => $cloverReportFilename,
                    'name' => 'SomeName',
                ],
                'crap4j' => [
                    'target' => $crap4jReportFilename,
                    'name' => 'SomeName',
                    'threshold' => 30,
                ],
            ]
        );

        $reportService->generateReport($coverage);
        $cloverReport = file_get_contents($cloverReportFilename);
        $crap4jReport = file_get_contents($crap4jReportFilename);

        self::assertNotEmpty($cloverReport);
        self::assertNotEmpty($crap4jReport);

        $filesystem->remove($cloverReportFilename);
        $filesystem->remove($crap4jReportFilename);
    }
}
