<?php

namespace Siebels\Pedigree;

use Siebels\Pedigree\IO\OutputStream;
use Siebels\Pedigree\IO\StdoutOutputStream;

final class Config
{
    private ?OutputStream $output = null;
    /**
     * @var array<string>
     */
    private array $components = [];

    public function getOutput(): OutputStream
    {
        return $this->output ??= new StdoutOutputStream();
    }

    public function setOutput(OutputStream $output): self
    {
        $this->output = $output;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getComponents(): array
    {
        return $this->components;
    }

    public function addComponent(string $component): self
    {
        $this->components[] = $component;

        return $this;
    }
}