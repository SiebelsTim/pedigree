<?php

declare(strict_types=1);

namespace Siebels\Pedigree\Generation;

use Siebels\Pedigree\Generation\Model\GeneratorMethod;
use Siebels\Pedigree\Graph\Graph;
use Siebels\Pedigree\Graph\Model\Clazz;
use Siebels\Pedigree\Graph\Model\Component;
use Siebels\Pedigree\Graph\Model\ComponentMethod;
use Siebels\Pedigree\Util\ClassName;

final class ServiceCreationResolver
{
    /**
     * @var array<string, GeneratorMethod>
     */
    private array $generatorMethods = [];

    public function getServiceGeneratorMethod(Clazz $class): GeneratorMethod
    {
        return $this->generatorMethods[$class->getFqcn()];
    }

    public function read(Graph $graph, Component $component): void
    {
        foreach ($component->getMethods() as $method) {
            $this->analyseClass($graph, $component, $graph->getClass($method->getReturnType()));
        }
    }

    private function analyseClass(Graph $graph, Component $component, Clazz $class): void
    {
        if (isset($this->generatorMethods[$class->getFqcn()])) {
            return;
        }

        $dependencies = $graph->getDependencies($class->getFqcn());
        foreach ($dependencies as $dependency) {
            $this->analyseClass($graph, $component, $dependency);
        }

        $componentMethod = $component->findMethodForType($class);
        $needsToBeGenerated = !$component->isAbstract() || ($componentMethod?->isAbstract() ?? true);
        $visibility = 'public';

        if (null === $componentMethod) {
            $componentMethod = new ComponentMethod($this->getMethodNameForClass($class->getFqcn()), $class->getFqcn(), true);
            $needsToBeGenerated = true;
            $visibility = 'protected';
        }

        $this->generatorMethods[$class->getFqcn()] = new GeneratorMethod($componentMethod, $visibility, $needsToBeGenerated);
    }

    private function getMethodNameForClass(string $classString): string
    {
        return "get" . ClassName::normalize($classString);
    }
}