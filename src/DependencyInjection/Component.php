<?php

namespace Siebels\Pedigree\DependencyInjection;

use Siebels\Pedigree\Application;

interface Component
{
    public function getApplication(): Application;
}