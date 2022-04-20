<?php

namespace Siebels\Pedigree;

use Siebels\Pedigree\Graph\DependencyAnalyser;
use Siebels\Pedigree\IO\Files;

final class Application {
    public function __construct(
        private Config $config,
    ){
    }

	public function run(Files $files): int
    {
        $dependencyAnalyser = new DependencyAnalyser();
        foreach ($files->getFiles() as $file) {
            $dependencyAnalyser->readFile($file);
        }

        $graph = $dependencyAnalyser->getGraph();

        $o = $this->config->getOutput();
        $o->write(<<<PHP
            <?php\n\n
            class ServiceLocator {
            
            PHP
        );
        foreach ($graph->getEntries() as $classString => $dependencies) {
            $dependencyParams = implode(', ', array_map(fn(string $dependency) => "\$this->get$dependency()", $dependencies));
            $o->write(<<<PHP
                private ?$classString \$_$classString = null;
                public function get$classString(): $classString {
                    return \$this->_$classString ??= new $classString($dependencyParams);
                }


            PHP
            );
        }

        $o->write("}\n");
		return 0;
	}
}
