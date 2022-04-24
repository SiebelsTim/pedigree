<?php

declare(strict_types=1);

namespace Siebels\Pedigree\IO;

interface File
{
    public function getContent(): string;
}