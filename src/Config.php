<?php

declare(strict_types=1);

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

    /**
     * @var array<string>
     */
    private array $sourcePaths = [];

    private ?string $namespace = null;

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

    /**
     * @return string[]
     */
    public function getSourcePaths(): array
    {
        return $this->sourcePaths;
    }

    public function addSourcePath(string $path): self
    {
        $this->sourcePaths[] = $path;

        return $this;
    }

    public function getNamespace(): ?string
    {
        return $this->namespace;
    }

    public function setNamespace(?string $namespace): void
    {
        $this->namespace = $namespace;
    }
}