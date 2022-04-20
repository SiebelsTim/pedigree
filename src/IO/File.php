<?php

namespace Siebels\Pedigree\IO;

final class File
{
    public function __construct(
        private string $content,
    ) {
    }

    public function getContent(): string
    {
        return $this->content;
    }
}