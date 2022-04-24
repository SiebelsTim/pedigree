<?php

declare(strict_types=1);

namespace Siebels\Pedigree;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\Printer;
use Nette\PhpGenerator\Property;
use Siebels\Pedigree\Generation\Model\GeneratorMethod;
use Siebels\Pedigree\Generation\ServiceCreationResolver;
use Siebels\Pedigree\Graph\ComponentFinder;
use Siebels\Pedigree\Graph\DependencyGraphGenerator;
use Siebels\Pedigree\Graph\Model\Clazz;
use Siebels\Pedigree\Graph\Model\Component;
use Siebels\Pedigree\IO\Files;
use Siebels\Pedigree\Util\ClassName;

final class Processor {

    public function __construct(
        private DependencyGraphGenerator $dependencyAnalyser,
        private ComponentFinder          $componentFinder,
        private ServiceCreationResolver  $creationResolver,
    ) {
    }

    public function run(Config $config, Files $files): int
    {
        $o = $config->getOutput();
        $this->dependencyAnalyser->readFile(...$files->getFiles());
        $graph = $this->dependencyAnalyser->getGraph();

        foreach ($config->getComponents() as $component) {
            $componentModel = $this->componentFinder->findComponent($component, $graph);
            $this->creationResolver->read($graph, $componentModel);
            
            $file = $this->createComponentImplementation($componentModel, $graph, $config);
            $o->write((new Printer())->printFile($file));
        }

        return 0;
	}

    private function createComponentImplementation(Component $component, Graph\Graph $graph, Config $config): PhpFile
    {
        $file = new PhpFile();
        $ns = $file->addNamespace($config->getNamespace() ?? 'Pedigree');
        $class = $ns->addClass($this->getComponentName($component->getFqcn()));
        $class->addImplement($component->getFqcn());

        foreach ($component->getMethods() as $method) {
            $generatorMethod = $this->creationResolver->getServiceGeneratorMethod($graph->getClass($method->getReturnType()));

            $this->createMethodRecursively($generatorMethod, $graph, $class);
        }

        return $file;
    }

    private $existingMethods = [];
    private function createMethodRecursively(GeneratorMethod $generatorMethod, Graph\Graph $graph, ClassType $class): void
    {
        $classString = $generatorMethod->getMethod()->getReturnType();
        if (!$generatorMethod->isNeedsToBeGenerated() || isset($this->existingMethods[$classString])) {
            return;
        }

        $dependencies = $graph->getDependencies($classString);
        foreach ($dependencies as $dependency) {
            $this->createMethodRecursively($this->creationResolver->getServiceGeneratorMethod($graph->getClass($dependency->getFqcn())), $graph, $class);
        }
        $this->createDependencyMethod($dependencies, $generatorMethod, $class);
        $this->existingMethods[$classString] = $classString;
    }

    /**
     * @param array<Clazz> $dependencies
     */
    private function createDependencyMethod(array $dependencies, GeneratorMethod $generatorMethod, ClassType $class): void
    {
        $classString = $generatorMethod->getMethod()->getReturnType();
        $propertyName = $this->getPropertyNameForClass($classString);
        $property = (new Property($propertyName))
            ->setType($classString)
            ->setNullable()
            ->setPrivate()
            ->setValue(null)
        ;
        $class->addMember($property);

        $dependencyParams = implode(', ', array_map(fn(Clazz $dependency) => sprintf('$this->%s()', $this->creationResolver->getServiceGeneratorMethod($dependency)->getMethod()->getName()), $dependencies));
        $body = "return \$this->$propertyName ??= new \\$classString($dependencyParams);";
        $this->createMethod($class, $generatorMethod->getMethod()->getName(), $generatorMethod->getVisibility(), $body, $classString);
    }

    private function createMethod(ClassType $class, string $name, string $visibility, string $body, string $returnType): Method
    {
        $method = (new Method($name))
            ->setReturnType($returnType)
            ->setVisibility($visibility)
        ;
        $class->addMember($method);
        $method->addBody($body);

        return $method;
    }

    private function getPropertyNameForClass(string $classString): string
    {
        return "_" . ClassName::normalize($classString);
    }

    private function getComponentName(string $componentClassString): string
    {
        if (str_contains($componentClassString, '\\')) {
            $componentClassString = substr($componentClassString, strrpos($componentClassString, '\\')+1);
        }

        return 'Pedigree' . $componentClassString;
    }
}
