<?php

declare(strict_types=1);

namespace Siebels\Pedigree\IO;

class InMemoryOutputStream implements OutputStream
{
    private string $content = '';

    public function write(string $content): void
    {
        $this->content .= $content;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}