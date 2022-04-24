<?php

declare(strict_types=1);

namespace Siebels\Pedigree\IO;

class FilesystemFile implements File
{
    public function __construct(
        private string $path,
    ) {
    }

    public function getContent(): string
    {
        return file_get_contents($this->path);
    }
}