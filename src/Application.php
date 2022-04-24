<?php

declare(strict_types=1);

namespace Siebels\Pedigree;

use Siebels\Pedigree\IO\Files;
use Siebels\Pedigree\IO\FilesystemFile;

final class Application {

    public function __construct(
        private Processor $processor
    ) {
    }

    public function run(Config $config): int
    {
        $files = [];
        foreach ($config->getSourcePaths() as $sourcePath) {
            /** @var \SplFileInfo $path */
            foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($sourcePath)) as $path) {
                if ($path->getType() !== 'file') {
                    continue;
                }

                $files[] = new FilesystemFile($path->getRealPath());
            }
        }

        return $this->processor->run($config, new Files($files));
	}
}
