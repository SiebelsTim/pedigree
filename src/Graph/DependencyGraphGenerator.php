<?php

declare(strict_types=1);

namespace Siebels\Pedigree\Graph;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\FindingVisitor;
use Siebels\Pedigree\Graph\Model\Clazz;
use Siebels\Pedigree\IO\File;
use Siebels\Pedigree\Parser;

final class DependencyGraphGenerator
{
    private Graph $graph;

    public function __construct(
        private Parser $parser,
    ) {
        $this->graph = new Graph();
    }

    public function readFile(File ...$files): void
    {
        foreach ($files as $file) {
            $this->readSingleFile($file);
        }
    }

    private function readSingleFile(File $file): void
    {
        $stmts = $this->parser->parse($file);

        $nodeTraverser = new NodeTraverser();
        $finder = new FindingVisitor(fn(Node $node) => $node instanceof Class_ || $node instanceof Node\Stmt\Interface_);
        $nodeTraverser->addVisitor($finder);
        $nodeTraverser->traverse($stmts);

        /** @var Class_ $foundNode */
        foreach ($finder->getFoundNodes() as $foundNode) {
            $class = new Clazz($foundNode->namespacedName->toString(), $file, []);
            if (null !== ($ctor = $foundNode->getMethod('__construct')) && [] !== ($params = $ctor->getParams())) {
                $class->addDependency(...array_map(fn(Node\Param $param) => $param->type?->toString(), $params));
            }
            $this->graph->addEntry($class);
        }
    }

    public function getGraph(): Graph
    {
        return $this->graph;
    }
}