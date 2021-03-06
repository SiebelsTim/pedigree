<?php

declare(strict_types=1);

namespace Siebels\Pedigree\DependencyInjection;

use Siebels\Pedigree\Application;
use Siebels\Pedigree\Processor;

interface Component
{
    public function getApplication(): Application;
    public function getProcessor(): Processor;
}