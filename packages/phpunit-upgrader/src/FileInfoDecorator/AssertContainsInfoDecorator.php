<?php

declare(strict_types=1);

namespace Migrify\PHPUnitUpgrader\FileInfoDecorator;

use Migrify\PHPUnitUpgrader\AssertContainsFileLineExtractor;
use Migrify\PHPUnitUpgrader\ValueObject\FilePathWithContent;
use Nette\Utils\Strings;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Migrify\PHPUnitUpgrader\Tests\FileInfoDecorator\AssertContainsInfoDecorator\AssertContainsInfoDecoratorTest
 */
final class AssertContainsInfoDecorator
{
    /**
     * @var AssertContainsFileLineExtractor
     */
    private $assertContainsFileLineExtractor;

    public function __construct(AssertContainsFileLineExtractor $assertContainsFileLineExtractor)
    {
        $this->assertContainsFileLineExtractor = $assertContainsFileLineExtractor;
    }

    public function decorate(FilePathWithContent $filePathWithContent, SmartFileInfo $errorReportFileInfo): string
    {
        $fileLines = $this->assertContainsFileLineExtractor->extract($errorReportFileInfo);

        $currentFileLineContents = $filePathWithContent->getContentLines();

        foreach ($fileLines as $fileLine) {
            if (! Strings::endsWith($fileLine->getFilePath(), $filePathWithContent->getFilePath())) {
                continue;
            }

            foreach ($currentFileLineContents as $currentLineNumber => $currentLineContent) {
                if ($fileLine->getLine() !== $currentLineNumber) {
                    continue;
                }

                $newLineContent = Strings::replace(
                    $currentLineContent,
                    '#assertContains#',
                    'assertStringContainsString'
                );

                if ($newLineContent === $currentLineContent) {
                    continue;
                }

                $filePathWithContent->changeLineContent($currentLineNumber, $newLineContent);
            }
        }

        return $filePathWithContent->getCurrentFileContent();
    }
}
