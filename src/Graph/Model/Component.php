<?php

declare(strict_types=1);

namespace Siebels\Pedigree\Graph\Model;

final class Component
{
    /**
     * @param ComponentMethod[] $methods
     */
    public function __construct(
        private string $fqcn,
        private array $methods,
    ) {
    }

    public function getFqcn(): string
    {
        return $this->fqcn;
    }

    /**
     * @return ComponentMethod[]
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    public function findMethodForType(Clazz $type): ?ComponentMethod
    {
        foreach ($this->getMethods() as $method) {
            if ($type->getFqcn() === $method->getReturnType()) {
                return $method;
            }
        }

        return null;
    }
}