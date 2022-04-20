<?php

namespace Siebels\Pedigree\Graph;

final class Graph
{
    // map from class-string to array of class-string
    private array $dependencies = [];

    public function getDependencies(string $classString): array
    {
        return $this->dependencies[$classString];
    }

    public function addEntry(string $classString, array $dependencies): void
    {
        $this->dependencies[$classString] = $dependencies;
    }

    public function getEntries(): array
    {
        return $this->dependencies;
    }
}