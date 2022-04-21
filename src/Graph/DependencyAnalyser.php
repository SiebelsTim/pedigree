<?php

namespace Siebels\Pedigree\Graph;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\FindingVisitor;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use Siebels\Pedigree\IO\File;

final class DependencyAnalyser
{
    private Graph $graph;
    private Parser $parser;

    public function __construct()
    {
        $this->graph = new Graph();
        $this->parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
    }

    public function readFile(File ...$files): void
    {
        foreach ($files as $file) {
            $this->readSingleFile($file);
        }
    }

    private function readSingleFile(File $file): void
    {
        $stmts = $this->parse($file);

        $nodeTraverser = new NodeTraverser();
        $finder = new FindingVisitor(fn(Node $node) => $node instanceof Class_);
        $nodeTraverser->addVisitor($finder);
        $nodeTraverser->traverse($stmts);

        /** @var Class_ $foundNode */
        foreach ($finder->getFoundNodes() as $foundNode) {
            if (null === $ctor = $foundNode->getMethod('__construct')) {
                $this->graph->addEntry($foundNode->namespacedName->toString(), []);
            } else if ([] === $params = $ctor->getParams()) {
                $this->graph->addEntry($foundNode->namespacedName->toString(), []);
            } else {
                $this->graph->addEntry($foundNode->namespacedName->toString(), array_map(fn (Node\Param $param) => $param->type?->toString(), $params));
            }
        }
    }

    public function getGraph(): Graph
    {
        return $this->graph;
    }

    /**
     * @return Node\Stmt[]
     */
    private function parse(File $file): array
    {
        $stmts = $this->parser->parse($file->getContent());
        $nameResolver = new NameResolver();
        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor($nameResolver);

        // Resolve names
        return $nodeTraverser->traverse($stmts);
    }
}