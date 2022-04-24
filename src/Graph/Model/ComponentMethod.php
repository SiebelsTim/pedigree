<?php

declare(strict_types=1);

namespace Siebels\Pedigree\Graph\Model;

final class ComponentMethod
{
    public function __construct(
        private string $name,
        private string $returnType,
    ) {
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getReturnType(): string
    {
        return $this->returnType;
    }
}