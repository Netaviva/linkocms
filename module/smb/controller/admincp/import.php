<?php

defined('LINKO') or exit();

/**
 * @author Morrison Laju <morrelinko@gmail.com>
 * @package linkocms
 */
class Smb_Controller_Admincp_Import extends Linko_Controller
{
    public function main()
    {
        if(Input::post('import'))
        {
            /**
             * @var $oUpload Linko_Upload
             */
            $oUpload = Linko::Upload()->setAllowedType(array('zip'))
                ->setOverwrite(true);

            $oUpload->load('backup');

            list($bReturn, $sMsg) = Linko::Model('Smb/Action')->import($oUpload->getFile());

            if($bReturn)
            {
                Linko::Flash()->success("Module Backup Imported Successfully!");
            }
            else
            {
                Linko::Flash()->warning($sMsg);
            }

            Linko::Response()->redirect('smb:admincp');
        }

        Linko::Template()
            ->setBreadcrumb(array(
            'Super Module Backup' => Linko::Url()->make('smb:admincp'),
                'Import Backup'
            ), 'Import Backup')
            ->setTitle('Import Backup')
            ->setVars(array(

            ));
    }
}