<?php

declare(strict_types=1);

namespace Siebels\Pedigree\Generation\Model;

use Siebels\Pedigree\Graph\Model\ComponentMethod;

final class GeneratorMethod
{
    public function __construct(
        private ComponentMethod $method,
        private string $visibility,
        private bool $needsToBeGenerated,
    ) {
    }

    public function getMethod(): ComponentMethod
    {
        return $this->method;
    }

    public function isNeedsToBeGenerated(): bool
    {
        return $this->needsToBeGenerated;
    }

    public function getVisibility(): string
    {
        return $this->visibility;
    }
}