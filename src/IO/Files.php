<?php

namespace Siebels\Pedigree\IO;

use Siebels\Pedigree\IO\File;

final class Files
{
    /**
     * @param array<File> $files
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