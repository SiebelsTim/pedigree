<?php

declare(strict_types=1);

namespace Siebels\Pedigree\Graph\Model;

use Siebels\Pedigree\IO\File;
use Siebels\Pedigree\IO\InMemoryFile;

final class Clazz
{
    /**
     * @param array<string> $dependencies
     */
    public function __construct(
        private string $fqcn,
        private File   $file,
        private array  $dependencies = [],
    ) {
    }

    public function getFqcn(): string
    {
        return $this->fqcn;
    }

    public function getFile(): File
    {
        return $this->file;
    }

    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return $this->dependencies;
    }

    public function addDependency(string ...$dependency): self
    {
        foreach ($dependency as $item) {
            $this->dependencies[] = $item;
        }

        return $this;
    }
}