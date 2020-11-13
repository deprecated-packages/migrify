<?php

declare(strict_types=1);

namespace Migrify\ConfigPretifier\Command;

use Migrify\ConfigPretifier\Pretifier\NeonConfigPretifier;
use Migrify\ConfigPretifier\ValueObject\Option;
use Migrify\MigrifyKernel\Command\AbstractMigrifyCommand;
use Migrify\MigrifyKernel\Exception\NotImplementedYetException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\PackageBuilder\Console\ShellCode;

final class PretifyCommand extends AbstractMigrifyCommand
{
    /**
     * @var NeonConfigPretifier
     */
    private $neonConfigPretifier;

    public function __construct(NeonConfigPretifier $neonConfigPretifier)
    {
        parent::__construct();

        $this->neonConfigPretifier = $neonConfigPretifier;
    }

    protected function configure(): void
    {
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
