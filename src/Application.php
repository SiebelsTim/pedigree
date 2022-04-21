<?php

namespace Siebels\Pedigree;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\Printer;
use Nette\PhpGenerator\Property;
use Siebels\Pedigree\Graph\DependencyAnalyser;
use Siebels\Pedigree\IO\Files;

final class Application {
    public function __construct(
        private Config $config,
    ){
    }

	public function run(Files $files): int
    {
        $o = $this->config->getOutput();
        $dependencyAnalyser = new DependencyAnalyser();
        $dependencyAnalyser->readFile(...$files->getFiles());
        $graph = $dependencyAnalyser->getGraph();

        $file = $this->createFile($graph);

        $o->write((new Printer())->printFile($file));

		return 0;
	}

    private function createFile(Graph\Graph $graph): PhpFile
    {
        $file = new PhpFile();
        $ns = $file->addNamespace('Pedigree');
        $class = $ns->addClass('ServiceLocator');
        foreach ($graph->getEntries() as $classString => $dependencies) {
            $this->createMethod($dependencies, $classString, $class);
        }
        return $file;
    }

    /**
     * @param array<string> $dependencies
     * @param string $classString
     * @param ClassType $class
     */
    private function createMethod(mixed $dependencies, string $classString, ClassType $class): void
    {
        $propertyName = $this->getPropertyNameForClass($classString);
        $property = (new Property($propertyName))
            ->setType($classString)
            ->setNullable()
            ->setPrivate()
            ->setValue(null)
        ;
        $class->addMember($property);
        $method = (new Method($this->getMethodNameForClass($classString)))
            ->setPublic()
            ->setReturnType($classString)
        ;
        $class->addMember($method);

        $dependencyParams = implode(', ', array_map(fn(string $dependency) => sprintf('$this->%s()', $this->getMethodNameForClass($dependency)), $dependencies));
        $method->addBody("return \$this->$propertyName ??= new \\$classString($dependencyParams);");
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
}
