<?php

namespace Siebels\Pedigree;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\Printer;
use Nette\PhpGenerator\Property;
use Siebels\Pedigree\Graph\ComponentFinder;
use Siebels\Pedigree\Graph\DependencyAnalyser;
use Siebels\Pedigree\Graph\Model\Clazz;
use Siebels\Pedigree\IO\Files;

final class Processor {

    public function __construct(
        private DependencyAnalyser $dependencyAnalyser,
        private ComponentFinder $componentFinder,
    ) {
    }

    public function run(Config $config, Files $files): int
    {
        $o = $config->getOutput();
        $this->dependencyAnalyser->readFile(...$files->getFiles());
        $graph = $this->dependencyAnalyser->getGraph();

        foreach ($config->getComponents() as $component) {
            $file = $this->createComponent($component, $graph, $config);
            $o->write((new Printer())->printFile($file));
        }

        return 0;
	}

    private function createComponent(string $componentClassString, Graph\Graph $graph, Config $config): PhpFile
    {
        $file = new PhpFile();
        $ns = $file->addNamespace($config->getNamespace() ?? 'Pedigree');
        $class = $ns->addClass($this->getComponentName($componentClassString));
        $class->addImplement($componentClassString);

        $component = $this->componentFinder->findComponent($componentClassString, $graph);
        foreach ($component->getMethods() as $method) {
            $callsMethod = $this->getMethodNameForClass($method->getReturnType());
            $this->createMethod($class, $method->getName(), sprintf('return $this->%s();', $callsMethod), $method->getReturnType())
                ->setPublic()
            ;
            $this->createMethodRecursively($method->getReturnType(), $graph, $class);
        }

        return $file;
    }

    private $existingMethods = [];
    private function createMethodRecursively(string $classString, Graph\Graph $graph, ClassType $class): void
    {
        if (isset($this->existingMethods[$classString])) {
            return;
        }

        $dependencies = $graph->getDependencies($classString);
        foreach ($dependencies as $dependency) {
            $this->createMethodRecursively($dependency->getFqcn(), $graph, $class);
        }
        $this->createDependencyMethod(array_map(fn(Clazz $clazz) => $clazz->getFqcn(), $dependencies), $classString, $class);
        $this->existingMethods[$classString] = $classString;
    }

    /**
     * @param array<string> $dependencies
     * @param string $classString
     * @param ClassType $class
     */
    private function createDependencyMethod(mixed $dependencies, string $classString, ClassType $class): void
    {
        $propertyName = $this->getPropertyNameForClass($classString);
        $property = (new Property($propertyName))
            ->setType($classString)
            ->setNullable()
            ->setPrivate()
            ->setValue(null)
        ;
        $class->addMember($property);

        $dependencyParams = implode(', ', array_map(fn(string $dependency) => sprintf('$this->%s()', $this->getMethodNameForClass($dependency)), $dependencies));
        $body = "return \$this->$propertyName ??= new \\$classString($dependencyParams);";
        $this->createMethod($class, $this->getMethodNameForClass($classString), $body, $classString);
    }

    private function createMethod(ClassType $class, string $name, string $body, string $returnType): Method
    {
        $method = (new Method($name))
            ->setProtected()
            ->setReturnType($returnType)
        ;
        $class->addMember($method);
        $method->addBody($body);

        return $method;
    }

    private function getMethodNameForClass(string $classString): string
    {
        return "get" . $this->normalizeClassnameToMethodName($classString);
    }

    private function getPropertyNameForClass(string $classString): string
    {
        return "_" . $this->normalizeClassnameToMethodName($classString);
    }

    private function normalizeClassnameToMethodName(string $className): string
    {
        $ret = str_replace('_', '__', $className);
        $ret = str_replace('\\', '_', $className);

        return $ret;
    }

    private function getComponentName(string $componentClassString): string
    {
        if (str_contains($componentClassString, '\\')) {
            $componentClassString = substr($componentClassString, strrpos($componentClassString, '\\')+1);
        }

        return 'Pedigree' . $componentClassString;
    }
}
