<?php

use Siebels\Pedigree\Config;
use Siebels\Pedigree\DependencyInjection\Component;

return function(): Config {
    $config = new Config();

    $config->addSourcePath('src/');
    $config->addComponent(Component::class);
    $config->setNamespace('Siebels\\Pedigree\\DependencyInjection');

    return $config;
};