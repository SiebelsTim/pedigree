<?php

namespace Siebels\Pedigree\DependencyInjection;

class PedigreeComponent implements Component
{
	private ?\Siebels\Pedigree\Application $_Siebels_Pedigree_Application = null;


	public function getApplication(): \Siebels\Pedigree\Application
	{
		return $this->getSiebels_Pedigree_Application();
	}


	protected function getSiebels_Pedigree_Application(): \Siebels\Pedigree\Application
	{
		return $this->_Siebels_Pedigree_Application ??= new \Siebels\Pedigree\Application();
	}
}
