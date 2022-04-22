<?php

use Siebels\Pedigree\Config;
use Siebels\Pedigree\DependencyInjection\Component;

return function(): Config {
    $config = new Config();

    $config->addSourcePath('src/');
    $config->addComponent(Component::class);

    return $config;
};