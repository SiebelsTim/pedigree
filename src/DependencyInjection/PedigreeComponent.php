<?php

namespace Siebels\Pedigree\DependencyInjection;

class PedigreeComponent implements Component
{
	private ?\Siebels\Pedigree\Parser $_Siebels_Pedigree_Parser = null;
	private ?\Siebels\Pedigree\Graph\DependencyGraphGenerator $_Siebels_Pedigree_Graph_DependencyGraphGenerator = null;
	private ?\Siebels\Pedigree\Graph\ComponentFinder $_Siebels_Pedigree_Graph_ComponentFinder = null;
	private ?\Siebels\Pedigree\Generation\ServiceCreationResolver $_Siebels_Pedigree_Generation_ServiceCreationResolver = null;
	private ?\Siebels\Pedigree\Processor $_Siebels_Pedigree_Processor = null;
	private ?\Siebels\Pedigree\Application $_Siebels_Pedigree_Application = null;


	protected function getSiebels_Pedigree_Parser(): \Siebels\Pedigree\Parser
	{
		return $this->_Siebels_Pedigree_Parser ??= new \Siebels\Pedigree\Parser();
	}


	protected function getSiebels_Pedigree_Graph_DependencyGraphGenerator(): \Siebels\Pedigree\Graph\DependencyGraphGenerator
	{
		return $this->_Siebels_Pedigree_Graph_DependencyGraphGenerator ??= new \Siebels\Pedigree\Graph\DependencyGraphGenerator($this->getSiebels_Pedigree_Parser());
	}


	protected function getSiebels_Pedigree_Graph_ComponentFinder(): \Siebels\Pedigree\Graph\ComponentFinder
	{
		return $this->_Siebels_Pedigree_Graph_ComponentFinder ??= new \Siebels\Pedigree\Graph\ComponentFinder($this->getSiebels_Pedigree_Parser());
	}


	protected function getSiebels_Pedigree_Generation_ServiceCreationResolver(): \Siebels\Pedigree\Generation\ServiceCreationResolver
	{
		return $this->_Siebels_Pedigree_Generation_ServiceCreationResolver ??= new \Siebels\Pedigree\Generation\ServiceCreationResolver();
	}


	public function getProcessor(): \Siebels\Pedigree\Processor
	{
		return $this->_Siebels_Pedigree_Processor ??= new \Siebels\Pedigree\Processor($this->getSiebels_Pedigree_Graph_DependencyGraphGenerator(), $this->getSiebels_Pedigree_Graph_ComponentFinder(), $this->getSiebels_Pedigree_Generation_ServiceCreationResolver());
	}


	public function getApplication(): \Siebels\Pedigree\Application
	{
		return $this->_Siebels_Pedigree_Application ??= new \Siebels\Pedigree\Application($this->getProcessor());
	}
}
