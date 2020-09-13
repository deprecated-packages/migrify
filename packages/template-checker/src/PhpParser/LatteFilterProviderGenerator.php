<?php

declare(strict_types=1);

namespace Migrify\TemplateChecker\PhpParser;

use Migrify\TemplateChecker\NodeFactory\GetNameClassMethodFactory;
use Migrify\TemplateChecker\NodeFactory\InvokeClassMethodFactory;
use Migrify\TemplateChecker\ValueObject\ClassMethodName;
use PhpParser\Builder\Class_ as ClassBuilder;
use PhpParser\Builder\Namespace_;
use PhpParser\Node\Const_;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\PrettyPrinter\Standard;

/**
 * @see \Migrify\TemplateChecker\Tests\PhpParser\LatteFilterProviderGenerator\LatteFilterProviderGeneratorTest
 */
final class LatteFilterProviderGenerator
{
    /**
     * @var string
     */
    private const LATTE_FILTER_PROVIDER_INTERFACE_NAME = 'App\Contract\Latte\FilterProviderInterface';

    /**
     * @var string
     */
    private const NAMESPACE_NAME = 'App\Latte\FilterProvider';

    /**
     * @var Standard
     */
    private $printerStandard;

    /**
     * @var InvokeClassMethodFactory
     */
    private $invokeClassMethodFactory;

    /**
     * @var GetNameClassMethodFactory
     */
    private $getNameClassMethodFactory;

    public function __construct(
        Standard $printerStandard,
        InvokeClassMethodFactory $invokeClassMethodFactory,
        GetNameClassMethodFactory $getNameClassMethodFactory
    ) {
        $this->printerStandard = $printerStandard;
        $this->invokeClassMethodFactory = $invokeClassMethodFactory;
        $this->getNameClassMethodFactory = $getNameClassMethodFactory;
    }

    public function generate(ClassMethodName $classMethodName): string
    {
        $namespaceBuilder = new Namespace_(self::NAMESPACE_NAME);

        $filterProviderClass = $this->createFilterProviderClass($classMethodName);
        $namespaceBuilder->addStmt($filterProviderClass);
        $namespace = $namespaceBuilder->getNode();

        return $this->printerStandard->prettyPrintFile([$namespace]) . PHP_EOL;
    }

    private function createFilterNameConst(ClassMethodName $classMethodName): ClassConst
    {
        $const = new Const_('FILTER_NAME', new String_($classMethodName->getMethod()));
        $classConst = new ClassConst([$const]);
        $classConst->flags |= Class_::MODIFIER_PUBLIC;

        return $classConst;
    }

    private function createFilterProviderClass(ClassMethodName $classMethodName): Class_
    {
        $class = new ClassBuilder($classMethodName->getFilterProviderClassName());
        $class->makeFinal();
        $class->implement(new FullyQualified(self::LATTE_FILTER_PROVIDER_INTERFACE_NAME));

        // add filter name constant
        $classConst = $this->createFilterNameConst($classMethodName);
        $class->addStmt($classConst);

        // add getName method
        $getNameClassMethod = $this->getNameClassMethodFactory->create();
        $class->addStmt($getNameClassMethod);

        // add __invoke method
        $invokeClassMethod = $this->invokeClassMethodFactory->create($classMethodName);
        $class->addStmt($invokeClassMethod);

        return $class->getNode();
    }
}
