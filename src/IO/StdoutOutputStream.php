<?php

namespace Siebels\Pedigree\IO;

final class StdoutOutputStream implements OutputStream
{
    public function write(string $content): void
    {
        echo $content;
    }
}