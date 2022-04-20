<?php

namespace Siebels\Pedigree;

use Siebels\Pedigree\IO\OutputStream;

final class Config
{
    public function __construct(
        private OutputStream $output,
    ) {
    }

    public function getOutput(): OutputStream
    {
        return $this->output;
    }
}