<?php

declare(strict_types=1);

namespace Migrify\Psr4Switcher\Command;

use Migrify\MigrifyKernel\ValueObject\MigrifyOption;
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

final class GeneratePsr4ToPathsCommand extends Command
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

        $this->addArgument(MigrifyOption::SOURCES, InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Path to source');
        $this->addOption(Option::COMPOSER_JSON, null, InputOption::VALUE_REQUIRED, 'Path to composer.json');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->psr4SwitcherConfiguration->loadFromInput($input);

        $classesToFiles = $this->phpClassLoader->load($this->psr4SwitcherConfiguration->getSource());

        $psr4NamespacesToPaths = [];
        $classesToFilesWithMissedCommonNamespace = [];
        foreach ($classesToFiles as $class => $file) {
            $psr4NamespaceToPath = $this->psr4NamespaceToPathFactory->createFromClassAndFile($class, $file);
            if ($psr4NamespaceToPath === null) {
                $classesToFilesWithMissedCommonNamespace[$class] = $file;
                continue;
            }

            $psr4NamespacesToPaths[] = $psr4NamespaceToPath;
        }

        $psr4NamespaceToPaths = $this->psr4Filter->filter($psr4NamespacesToPaths);
        $this->printJsonAutoload($psr4NamespaceToPaths);

        $this->symfonyStyle->success('Done');

        foreach ($classesToFilesWithMissedCommonNamespace as $class => $file) {
            $message = sprintf('Class "%s" and file "%s" have no match in PSR-4 namespace', $class, $file);
            $this->symfonyStyle->warning($message);
        }

        return ShellCode::SUCCESS;
    }

    /**
     * @param Psr4NamespaceToPaths[] $psr4NamespacesToPaths
     */
    private function normalizePsr4NamespaceToPathsToJsonsArray(array $psr4NamespacesToPaths): array
    {
        $data = [];

        foreach ($psr4NamespacesToPaths as $psr4NamespaceToPaths) {
            $namespaceRoot = $this->normalizeNamespaceRoot($psr4NamespaceToPaths->getNamespace());
            $data[$namespaceRoot] = $this->resolvePaths($psr4NamespaceToPaths);
        }

        ksort($data);

        return $data;
    }

    /**
     * @param Psr4NamespaceToPaths[] $psr4NamespaceToPaths
     */
    private function printJsonAutoload(array $psr4NamespaceToPaths): void
    {
        $normalizedJsonArray = $this->normalizePsr4NamespaceToPathsToJsonsArray($psr4NamespaceToPaths);
        $composerData = [
            'autoload' => [
                'psr-4' => $normalizedJsonArray,
            ],
        ];
        $json = Json::encode($composerData, Json::PRETTY);

        $this->symfonyStyle->writeln($json);
    }

    private function normalizeNamespaceRoot(string $namespace): string
    {
        return rtrim($namespace, '\\') . '\\';
    }

    /**
     * @return string|string[]
     */
    private function resolvePaths(Psr4NamespaceToPaths $psr4NamespaceToPaths)
    {
        if (count($psr4NamespaceToPaths->getPaths()) > 1) {
            $paths = $psr4NamespaceToPaths->getPaths();
            sort($paths);
            return $paths;
        }

        return $psr4NamespaceToPaths->getPaths()[0];
    }
}
