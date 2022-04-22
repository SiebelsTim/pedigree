<?php

namespace Siebels\Pedigree\DependencyInjection;

class PedigreeComponent implements Component
{
	private ?\Siebels\Pedigree\Parser $_Siebels_Pedigree_Parser = null;
	private ?\Siebels\Pedigree\Graph\DependencyAnalyser $_Siebels_Pedigree_Graph_DependencyAnalyser = null;
	private ?\Siebels\Pedigree\Graph\ComponentFinder $_Siebels_Pedigree_Graph_ComponentFinder = null;
	private ?\Siebels\Pedigree\Processor $_Siebels_Pedigree_Processor = null;
	private ?\Siebels\Pedigree\Application $_Siebels_Pedigree_Application = null;


	public function getApplication(): \Siebels\Pedigree\Application
	{
		return $this->getSiebels_Pedigree_Application();
	}


	protected function getSiebels_Pedigree_Parser(): \Siebels\Pedigree\Parser
	{
		return $this->_Siebels_Pedigree_Parser ??= new \Siebels\Pedigree\Parser();
	}


	protected function getSiebels_Pedigree_Graph_DependencyAnalyser(): \Siebels\Pedigree\Graph\DependencyAnalyser
	{
		return $this->_Siebels_Pedigree_Graph_DependencyAnalyser ??= new \Siebels\Pedigree\Graph\DependencyAnalyser($this->getSiebels_Pedigree_Parser());
	}


	protected function getSiebels_Pedigree_Graph_ComponentFinder(): \Siebels\Pedigree\Graph\ComponentFinder
	{
		return $this->_Siebels_Pedigree_Graph_ComponentFinder ??= new \Siebels\Pedigree\Graph\ComponentFinder($this->getSiebels_Pedigree_Parser());
	}


	protected function getSiebels_Pedigree_Processor(): \Siebels\Pedigree\Processor
	{
		return $this->_Siebels_Pedigree_Processor ??= new \Siebels\Pedigree\Processor($this->getSiebels_Pedigree_Graph_DependencyAnalyser(), $this->getSiebels_Pedigree_Graph_ComponentFinder());
	}


	protected function getSiebels_Pedigree_Application(): \Siebels\Pedigree\Application
	{
		return $this->_Siebels_Pedigree_Application ??= new \Siebels\Pedigree\Application($this->getSiebels_Pedigree_Processor());
	}


	public function getProcessor(): \Siebels\Pedigree\Processor
	{
		return $this->getSiebels_Pedigree_Processor();
	}
}
