<?php

class Module_Controller_Install extends Linko_Controller
{
	public function main()
	{
		$sLocation = $this->getParam('goto');

		if($sLocation == null)
		{
			$sLocation = 'upload';
		}

		Linko::Template()->setStyle('module.css', 'module_module');

		return Linko::Module()->set('module/goto/' . $sLocation, $this->getParam());
	}
}