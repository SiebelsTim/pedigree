<?php

declare(strict_types=1);

namespace Siebels\Pedigree\Graph\Model;

final class ComponentMethod
{
    public function __construct(
        private string $name,
        private string $returnType,
        private bool   $isAbstract,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getReturnType(): string
    {
        return $this->returnType;
    }

    public function isAbstract(): bool
    {
        return $this->isAbstract;
    }
}