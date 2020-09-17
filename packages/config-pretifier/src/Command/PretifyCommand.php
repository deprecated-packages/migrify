<?php

declare(strict_types=1);

namespace Migrify\ConfigPretifier\Command;

use Migrify\ConfigPretifier\Pretifier\NeonConfigPretifier;
use Migrify\ConfigPretifier\ValueObject\Option;
use Migrify\MigrifyKernel\Exception\NotImplementedYetException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\SmartFileSystem\Finder\SmartFinder;

final class PretifyCommand extends Command
{
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var NeonConfigPretifier
     */
    private $neonConfigPretifier;

    /**
     * @var SmartFinder
     */
    private $smartFinder;

    public function __construct(
        SymfonyStyle $symfonyStyle,
        SmartFinder $smartFinder,
        NeonConfigPretifier $neonConfigPretifier
    ) {
        parent::__construct();

        $this->symfonyStyle = $symfonyStyle;
        $this->smartFinder = $smartFinder;
        $this->neonConfigPretifier = $neonConfigPretifier;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Pretify NEON/YAML syntax in provided files');
        $this->addArgument(
            Option::SOURCES,
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'Paths to s/directories with NEON/YAML'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string[] $source */
        $source = (array) $input->getArgument(Option::SOURCES);

        $fileInfos = $this->smartFinder->find($source, '#\.(neon|yml|yaml)$#');

        // @todo refactor to collector later
        $supportedSuffixes = [NeonConfigPretifier::NEON_SUFFIX];

        foreach ($fileInfos as $fileInfo) {
            $message = sprintf('Processing "%s"', $fileInfo->getRelativeFilePathFromCwd());
            $this->symfonyStyle->title($message);

            if ($fileInfo->getSuffix() === NeonConfigPretifier::NEON_SUFFIX) {
                $newContent = $this->neonConfigPretifier->pretify($fileInfo->getContents());
                if ($newContent === null) {
                    continue;
                }
            } else {
                $message = sprintf(
                    'Suffix "%s" is not supported yet. Pick one of: "%s"',
                    $fileInfo->getSuffix(),
                    implode('", ', $supportedSuffixes)
                );
                throw new NotImplementedYetException($message);
            }

            $message = sprintf('File "%s" was made pretty', $fileInfo->getRelativeFilePathFromCwd());
            $this->symfonyStyle->writeln($message);
        }

        $this->symfonyStyle->success('OK');

        return ShellCode::SUCCESS;
    }
}
