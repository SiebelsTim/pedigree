<?php

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
    private Parser $parser;

    public function __construct()
    {
        $this->parser = new Parser();
    }

    public function findComponent(string $classString, Graph $graph): Component
    {
        $clazz = $graph->getClass($classString);
        $file = $clazz->getFile();
        $stmts = $this->parser->parse($file);

        $nodeTraverser = new NodeTraverser();
        $finder = new FindingVisitor(fn(Node $node) => $node instanceof Class_ && $node->namespacedName->toString() === $classString);
        $nodeTraverser->addVisitor($finder);
        $classAST = $nodeTraverser->traverse($stmts)[0];

        $finder = new FindingVisitor(fn(Node $node) => $node instanceof Node\Stmt\ClassMethod);
        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor($finder);
        $nodeTraverser->traverse([$classAST]);
        $methods = $finder->getFoundNodes();

        return new Component(array_map(fn (Node\Stmt\ClassMethod $method) => new ComponentMethod($method->name->toString(), $method->getReturnType()), $methods));
    }
}