<?php

namespace Siebels\Pedigree;

use PhpParser\Node\Stmt;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\ParserFactory;
use Siebels\Pedigree\IO\File;
use Siebels\Pedigree\IO\InMemoryFile;

class Parser
{
    /**
     * @return Stmt[]
     */
    public function parse(File $file): array
    {
        $stmts = (new ParserFactory())->create(ParserFactory::PREFER_PHP7)->parse($file->getContent());
        $nameResolver = new NameResolver();
        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor($nameResolver);

        // Resolve names
        return $nodeTraverser->traverse($stmts);
    }
}