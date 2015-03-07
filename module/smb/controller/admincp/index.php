<?php

defined('LINKO') or exit();

/**
 * @author Morrison Laju <morrelinko@gmail.com>
 * @package linkocms
 */
class Smb_Controller_Admincp_Index extends Linko_Controller
{
    public function main()
    {
        $aModules = Linko::Model('Module')->getModules();

        if(($aBackup = Input::post('backup')) && ($sModule = key($aBackup)))
        {
            if($sFile = Linko::Model('Smb/Action')->backup($sModule))
            {
                Linko::Response()->download($sFile);
            }
        }

        Linko::Template()->setBreadcrumb(array(
            'Super Module Backup',
        ), 'Super Module Backup')
        ->setTitle('Super Module Backup')
        ->setVars(array(
            'aModules' => $aModules
        ));
    }
}