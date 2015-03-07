<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV Team
 * @package linkocms
 * @subpackage install : model - install.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */
class Install_Model_Install extends Linko_Model
{
    private $_aSteps = array(
        'license' => 'License',
        'precheck' => 'Pre Installation Check',
        'configuration' => 'Configuration',
        'process' => 'Processing',
        'finalize' => 'Finalize',
	    'finish' => 'Finish'
    );
    
    /**
     * Holds the current step
     * 
     * @var string
     */   
    private $_sStep;
    
    /**
     * Holds the current installation id
     * 
     * @var string
     */   
    private $_sId;

	private $_bUpgrade = false;

	public function __construct()
    {
	    if($this->isInstalled())
	    {
			$this->_bUpgrade = true;
	    }
    }

    /**
     * run installation
     *
     * @param Linko_Controller $controller
     */
    public function run(Linko_Controller $controller)
    {
	    $this->_sStep = $controller->getParam('step');

	    if(!$this->_sStep)
	    {
			$this->_sStep = $this->getFirstStep();
	    }

	    if(!$this->_sId = $this->getOption('install_id'))
        {
            $this->_sId = $this->setOption('install_id', uniqid());
        }
        
        if(!array_key_exists($this->_sStep, $this->_aSteps))
        {
            exit('Invalid Step');
        }
        
        $sMethod = '_step_' . $this->_sStep;

        if(method_exists($controller, $sMethod))
        {
	        if(($this->isStepPassed($this->_sStep)
		      ||  (($this->_sStep != $this->getFirstStep()) && !$this->isStepPassed($this->getPrevStep()))
	        ))
	        {
		        $this->gotoStep($this->getNextExecuteStep(), false);
	        }

            call_user_func(array($controller, $sMethod));
        }
    }

    public function isInstalled()
    {
        return File::exists(INCLUDE_DIR . 'config' . DS . 'database.php') && Linko::Config()->get('application.installed');
    }
    
    public function getStep()
    {
        return $this->_sStep;
    }

    public function getSteps()
    {
        return $this->_aSteps;
    }
    
    public function gotoStep($sStep, $bPassed = true)
    {
	    if($bPassed)
	    {
		    $this->addToExecutedSteps($this->_sStep);
	    }
        
        if($sStep)
        {
            Linko::Response()->redirect(Linko::Url()->make('install', array('step' => $sStep)));
        }
    }

    public function getFirstStep()
    {
        return key($this->_aSteps);
    }
     
    public function getLastStep()
    {
        return key(array_slice($this->_aSteps, -1));
    }
     
    public function getNextStep()
    {
        $sStep = null;
        
        $bCurrent = false;
        
        foreach($this->_aSteps as $sKey => $sValue)
        {
            if($bCurrent)
            {
                $sStep = $sKey;
                
                break;
            }
            
            if($sKey == $this->_sStep)
            {
                $bCurrent = true;
            }
        }
        
        return $sStep;
    }
    
    public function getPrevStep()
    {
        $sStep = null;

	    foreach($this->_aSteps as $sKey => $sValue)
	    {
	        if($sKey == $this->_sStep)
		    {
			    break;
		    }

		    $sStep = $sKey;
	    }

	    return $sStep;
    }
    
    public function getRequirement()
    {
        $aRequirement = array(
            array(
                'title' => 'Php Version >= 5.3.1',
                'value' => version_compare(PHP_VERSION, '5.3.1', '>='),
                'extra' => null,
            ),
            array(
                'title' => 'Magic Quotes GPC Disabled',
                'value' => (ini_get('magic_quotes_gpc') == false),
                'extra' => null,
            ),
            array(
                'title' => 'Register Globals Off',
                'value' => (ini_get('register_globals') == false),
                'extra' => null,
            ),
            array(
                'title' => 'XML Support',
                'value' => extension_loaded('xml'),
                'extra' => null,
            )
        );
        
        return $aRequirement;
    } 
     
    public function getPhpSettings()
    {
        $aSettings = array(
            array(
                'title' => 'Safe Mode',
                'recommend' => 'Off',
                'value' => ini_get('safe_mode') ? 'On' : 'Off'
            )
        );
        
        foreach($aSettings as $iKey => $aSetting)
        {
            if(is_bool($aSetting['recommend']) || is_bool($aSetting['value']))
            {
                $aSettings[$iKey]['recommend'] = ($aSetting['recommend'] == true ? 'Yes' : 'no');
                $aSettings[$iKey]['value'] = ($aSetting['value'] == true ? 'Yes' : 'no');
            }
        }
        
        return $aSettings;
    }

	public function writeDatabaseConfig($aVals)
	{
		if(!Arr::hasKeys($aVals, 'driver', 'hostname', 'username', 'password', 'database', 'prefix'))
		{
			return Linko::Error()->set('Invalid form.');
		}

		$aReplace = array(
			'[DB_DRIVER]' => $aVals['driver'],
			'[DB_HOST]' => $aVals['hostname'],
			'[DB_USER]' => $aVals['username'],
			'[DB_PASS]' => $aVals['password'],
			'[DB_NAME]' => $aVals['database'],
			'[DB_PREFIX]' => $aVals['prefix']
		);

		if(($sFile = INCLUDE_DIR . 'config'. DS . 'database.php.old') && (!File::exists($sFile)))
		{
			return Linko::Error()->set('There was an error loading database configuration template. Cannot continue installation.');
		}

		$sConfig = str_replace(array_keys($aReplace), array_values($aReplace), File::read($sFile));

		if(File::write(INCLUDE_DIR . 'config'. DS . 'database.php', $sConfig, null, true))
		{
			return true;
		}

		return false;
	}

	public function getNextExecuteStep()
	{
		foreach($this->_aSteps as $sStep => $sTitle)
		{
			if($this->isStepPassed($sStep))
			{
				continue;
			}

			break;
		}

		return $sStep;
	}

    public function isStepPassed($sStep)
    {
        return $this->inExecutedSteps($sStep);
    }

	public function addToExecutedSteps($sStep)
	{
		$aSteps = $this->getOption('executed_steps');

		$aSteps[$sStep] = $sStep;

		$this->setOption('executed_steps', $aSteps);
	}

	public function inExecutedSteps($sStep)
	{
		$aSteps = (array)$this->getOption('executed_steps');

		if(in_array($sStep, $aSteps))
		{
			return true;
		}

		return false;
	}

	public function getOption($sName)
	{
		$sJson = Linko::Session()->get('install.sessionid');

		$aOptions = $sJson ? json_decode($sJson, true) : array();

		return isset($aOptions[$sName]) ? unserialize($aOptions[$sName]) : null;
	}

	public function setOption($sName, $sValue)
	{
		$sJson = Linko::Session()->get('install.sessionid');

		$aOptions = $sJson ? json_decode($sJson, true) : array();

		$aOptions[$sName] = serialize($sValue);

		Linko::Session()->set('install.sessionid', json_encode($aOptions));
	}

	public function removeOption($sName)
	{
		$sJson = Linko::Session()->get('install.sessionid');

		$aOptions = $sJson ? json_decode($sJson, true) : array();

		unset($aOptions[$sName]);

		Linko::Session()->set('install.sessionid', json_encode($aOptions));
	}

	public function clearOptions()
	{
		Linko::Session()->remove('install.sessionid');
	}

    private function _getStepFile($sStep)
    {
        return Linko::Config()->get('dir.log') . $this->_sId . '_' . $sStep . '.log.php';
    }
}

?>