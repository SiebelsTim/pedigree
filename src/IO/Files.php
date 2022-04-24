<?php

declare(strict_types=1);

namespace Siebels\Pedigree\IO;

use Siebels\Pedigree\IO\InMemoryFile;

final class Files
{
    /**
     * @param array<InMemoryFile> $files
     */
    public function __construct(
        private array $files,
    ) {
    }

    /**
     * @return File[]
     */
    public function getFiles(): array
    {
        return $this->files;
    }
}