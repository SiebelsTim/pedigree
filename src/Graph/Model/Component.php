<?php

namespace Siebels\Pedigree\Graph\Model;

final class Component
{
    /**
     * @param ComponentMethod[] $methods
     */
    public function __construct(
        private array $methods
    ) {
    }

    /**
     * @return ComponentMethod[]
     */
    public function getMethods(): array
    {
        return $this->methods;
    }
}