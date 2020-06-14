<?php

declare(strict_types=1);

namespace DVDoug\Behat\CodeCoverage\Test;

use DVDoug\Behat\CodeCoverage\Service\ReportService;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Driver\Driver;
use SebastianBergmann\CodeCoverage\Filter;
use Symfony\Component\Filesystem\Filesystem;

class ReportServiceTest extends TestCase
{
    public function testCanGenerateTextReport(): void
    {
        $driver = $this->createMock(Driver::class);
        $coverage = new CodeCoverage($driver, new Filter());

        $reportService = new ReportService(
            [
                'text' => [
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

        $driver = $this->createMock(Driver::class);
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

    public function testCanGenerateCrap4jReport(): void
    {
        $filesystem = new Filesystem();
        $reportFilename = sys_get_temp_dir() . '/crap4j.xml';
        $filesystem->remove($reportFilename);

        $driver = $this->createMock(Driver::class);
        $coverage = new CodeCoverage($driver, new Filter());

        $reportService = new ReportService(
            [
                'crap4j' => [
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

    public function testCanGeneratePHPReport(): void
    {
        $filesystem = new Filesystem();
        $reportFilename = sys_get_temp_dir() . '/report.php';
        $filesystem->remove($reportFilename);

        $driver = $this->createMock(Driver::class);
        $coverage = new CodeCoverage($driver, new Filter());

        $reportService = new ReportService(
            [
                'php' => [
                    'target' => $reportFilename,
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

        $driver = $this->createMock(Driver::class);
        $coverage = new CodeCoverage($driver, new Filter());

        $reportService = new ReportService(
            [
                'html' => [
                    'target' => $reportDirectory,
                    'lowUpperBound' => 50,
                    'highLowerBound' => 90,
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

        $driver = $this->createMock(Driver::class);
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
}
