<?php

declare(strict_types=1);

namespace Siebels\Pedigree\Graph;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\FindingVisitor;
use Siebels\Pedigree\Graph\Model\Component;
use Siebels\Pedigree\Graph\Model\ComponentMethod;
use Siebels\Pedigree\Parser;

final class ComponentFinder
{
    public function __construct(
        private Parser $parser,
    ) {
    }

    public function findComponent(string $classString, Graph $graph): Component
    {
        $clazz = $graph->getClass($classString);
        $file = $clazz->getFile();
        $stmts = $this->parser->parse($file);

        $nodeTraverser = new NodeTraverser();
        $finder = new FindingVisitor(fn(Node $node) => ($node instanceof Class_ || $node instanceof Node\Stmt\Interface_) && $node->namespacedName->toString() === $classString);
        $nodeTraverser->addVisitor($finder);
        $nodeTraverser->traverse($stmts);
        $classAST = $finder->getFoundNodes()[0];

        $finder = new FindingVisitor(fn(Node $node) => $node instanceof Node\Stmt\ClassMethod);
        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor($finder);
        $nodeTraverser->traverse([$classAST]);
        $methods = $finder->getFoundNodes();


        return new Component($classString, array_map(fn (Node\Stmt\ClassMethod $method) => new ComponentMethod($method->name->toString(), $method->getReturnType()->toString()), $methods));
    }
}