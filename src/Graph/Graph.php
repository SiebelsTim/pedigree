<?php

declare(strict_types=1);

namespace Siebels\Pedigree\Graph;

use Siebels\Pedigree\Graph\Model\Clazz;

final class Graph
{
    /**
     * @var array<string, Clazz>
     */
    private array $classes = [];

    /**
     * @param string $classString
     * @return array<Clazz>
     */
    public function getDependencies(string $classString): array
    {
        return array_map(fn (string $classString) => $this->getClass($classString), $this->getClass($classString)->getDependencies());
    }

    public function getClass(string $class): Clazz
    {
        return $this->classes[$class] ?? throw new \RuntimeException("Class $class not found in graph");
    }

    public function addEntry(Clazz $class): void
    {
        $this->classes[$class->getFqcn()] = $class;
    }
}