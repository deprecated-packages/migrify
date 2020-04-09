<?php

declare(strict_types=1);

namespace Migrify\Psr4Switcher\Console\Command;

use Migrify\Psr4Switcher\Configuration\Psr4SwitcherConfiguration;
use Migrify\Psr4Switcher\Psr4Filter;
use Migrify\Psr4Switcher\RobotLoader\PhpClassLoader;
use Migrify\Psr4Switcher\ValueObject\Option;
use Migrify\Psr4Switcher\ValueObject\Psr4NamespaceToPaths;
use Migrify\Psr4Switcher\ValueObjectFactory\Psr4NamespaceToPathFactory;
use Nette\Utils\Json;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;

final class CheckCommand extends Command
{
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var Psr4SwitcherConfiguration
     */
    private $psr4SwitcherConfiguration;

    /**
     * @var PhpClassLoader
     */
    private $phpClassLoader;

    /**
     * @var Psr4NamespaceToPathFactory
     */
    private $psr4NamespaceToPathFactory;

    /**
     * @var Psr4Filter
     */
    private $psr4Filter;

    public function __construct(
        SymfonyStyle $symfonyStyle,
        Psr4SwitcherConfiguration $psr4SwitcherConfiguration,
        PhpClassLoader $phpClassLoader,
        Psr4NamespaceToPathFactory $psr4NamespaceToPathFactory,
        Psr4Filter $psr4Filter
    ) {
        $this->symfonyStyle = $symfonyStyle;
        $this->phpClassLoader = $phpClassLoader;
        $this->psr4SwitcherConfiguration = $psr4SwitcherConfiguration;
        $this->psr4NamespaceToPathFactory = $psr4NamespaceToPathFactory;
        $this->psr4Filter = $psr4Filter;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Check if application is PSR-4 ready');

        $this->addArgument(Option::SOURCE, InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Path to source');
        $this->addOption(Option::COMPOSER_JSON, null, InputOption::VALUE_REQUIRED, 'Path to composer.json');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->psr4SwitcherConfiguration->loadFromInput($input);

        $classesToFiles = $this->phpClassLoader->load($this->psr4SwitcherConfiguration->getSource());

        $psr4NamespacesToPaths = [];
        foreach ($classesToFiles as $class => $file) {
            $psr4NamespaceToPath = $this->psr4NamespaceToPathFactory->createFromClassAndFile($class, $file);
            if ($psr4NamespaceToPath === null) {
                continue;
            }

            $psr4NamespacesToPaths[] = $psr4NamespaceToPath;
        }

        $psr4NamespaceToPaths = $this->psr4Filter->filter($psr4NamespacesToPaths);

        $normalizedJsonArray = $this->normalizePsr4NamespaceToPathsToJsonsArray($psr4NamespaceToPaths);

        $composerData = [
            'autoload' => [
                'psr-4' => $normalizedJsonArray,
            ],
        ];

        $json = Json::encode($composerData, Json::PRETTY);
        $this->symfonyStyle->writeln($json);

        $this->symfonyStyle->success('Done');

        return ShellCode::SUCCESS;
    }

    /**
     * @param Psr4NamespaceToPaths[] $psr4NamespacesToPaths
     */
    private function normalizePsr4NamespaceToPathsToJsonsArray(array $psr4NamespacesToPaths): array
    {
        $data = [];
        foreach ($psr4NamespacesToPaths as $psr4NamespaceToPaths) {
            $paths = $psr4NamespaceToPaths->getPaths();
            if (count($paths) === 1) {
                $data[$psr4NamespaceToPaths->getNamespace() . '\\'] = $paths[0];
            } else {
                $data[$psr4NamespaceToPaths->getNamespace() . '\\'] = $paths;
            }
        }

        return $data;
    }
}
