<?php

declare(strict_types=1);

namespace Siebels\Pedigree\IO;

interface OutputStream
{
    public function write(string $content): void;
}