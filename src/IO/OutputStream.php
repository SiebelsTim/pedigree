<?php

namespace Siebels\Pedigree\IO;

interface OutputStream
{
    public function write(string $content): void;
}