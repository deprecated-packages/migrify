<?php

declare(strict_types=1);

namespace Migrify\ConfigFeatureBumper\Command;

use Migrify\ConfigFeatureBumper\ValueObject\Option;
use Migrify\ConfigFeatureBumper\Yaml\ExplicitToAutodiscoveryConverter;
use Migrify\MigrifyKernel\Command\AbstractMigrifyCommand;
use Nette\Utils\Strings;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;
use Symplify\PackageBuilder\Console\ShellCode;

final class BumpCommand extends AbstractMigrifyCommand
{
    /**
     * @var string
     */
    private const OPTION_NESTING_LEVEL = 'nesting-level';

    /**
     * @var string
     */
    private const OPTION_FILTER = 'filter';

    /**
     * @var ExplicitToAutodiscoveryConverter
     */
    private $explicitToAutodiscoveryConverter;

    public function __construct(ExplicitToAutodiscoveryConverter $explicitToAutodiscoveryConverter)
    {
        parent::__construct();

        $this->explicitToAutodiscoveryConverter = $explicitToAutodiscoveryConverter;
    }

    protected function configure(): void
    {
        $this->setDescription(
            'Convert "(services|config).(yml|yaml)" from pre-Symfony 3.3 format to modern format using autodiscovery, autowire and autoconfigure'
        );

        $this->addArgument(
            Option::SOURCES,
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'Path to your application directory or single config file'
        );

        $this->addOption(
            self::OPTION_NESTING_LEVEL,
            'l',
            InputOption::VALUE_REQUIRED,
            'How many namespace levels should be separated in autodiscovery, e.g 2 → "App\SomeProject\", 3 → "App\SomeProject\InnerNamespace\"',
            2
        );

        $this->addOption(
            self::OPTION_FILTER,
            'f',
            InputOption::VALUE_REQUIRED,
            'Only include service by filtered name, e.g. "--filter Controller"'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $sources = (array) $input->getArgument(Option::SOURCES);
        $fileInfos = $this->smartFinder->find($sources, '#\.(yml|yaml)$#');

        foreach ($fileInfos as $fileInfo) {
            $this->symfonyStyle->section('Processing ' . $fileInfo->getRealPath());

            $nestingLevel = (int) $input->getOption(self::OPTION_NESTING_LEVEL);
            $filter = (string) $input->getOption(self::OPTION_FILTER);

            $servicesYaml = Yaml::parse($fileInfo->getContents());

            $convertedYaml = $this->explicitToAutodiscoveryConverter->convert(
                $servicesYaml,
                $fileInfo->getRealPath(),
                $nestingLevel,
                $filter
            );

            if ($servicesYaml === $convertedYaml) {
                $this->symfonyStyle->note('No changes');
                continue;
            }

            $convertedContent = Yaml::dump($convertedYaml, Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK);

            // "SomeNamespace\SomeService: null" → "SomeNamespace\SomeService: ~"
            $convertedContent = Strings::replace($convertedContent, '#^( {4}([A-Z].*?): )(null)$#m', '$1~');

            // save
            $this->smartFileSystem->dumpFile($fileInfo->getRealPath(), $convertedContent);

            $this->symfonyStyle->note('File converted');
        }

        $this->symfonyStyle->success('Done');

        return ShellCode::SUCCESS;
    }
}
