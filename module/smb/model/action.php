<?php

defined('LINKO') or exit();

/**
 * @author Morrison Laju <morrelinko@gmail.com>
 * @package linkocms
 */
class Smb_Model_Action extends Linko_Model
{
    public function __construct()
    {
        if(!Dir::exists(DIR_STORAGE . 'smb' . DS))
        {
            Dir::create(DIR_STORAGE . 'smb' . DS);
        }
    }

    public function backup($sModule)
    {
        Dir::delete(DIR_STORAGE . 'smb' . DS . $sModule . DS, true);
        $aManifest = Linko::Model('Module')->getManifest($sModule);

        if(isset($aManifest['table']) && ($sManifest = trim($aManifest['table'])) != '')
        {
            $aTableDump = unserialize($sManifest);

            foreach(array_keys($aTableDump) as $sTable)
            {
                Linko::Xml()->toXml(Linko::Database()->table($sTable)
                    ->select()
                    ->query()
                    ->fetchRows(), array(
                        'sRootTag' => 'table',
                        'sItemTag' => 'row',
                        'aAttribute' => array('name' => $sTable)
                    ));

                $sXml = Linko::Xml()->output();

                File::write(DIR_STORAGE . 'smb' . DS . $sModule . DS . 'data' . DS . $sTable . '.xml', $sXml, null, true);
            }
        }

        File::write(DIR_STORAGE . 'smb' . DS . $sModule . DS . 'module.smb', $sModule, null, true);
        $oPcl = Linko::PclZip(DIR_STORAGE . 'smb' . DS . $sModule . '.zip');
        $oPcl->create(DIR_MODULE . $sModule . DS, PCLZIP_OPT_REMOVE_PATH, DIR_MODULE);
        $oPcl->add(array(
            DIR_STORAGE . 'smb' . DS . $sModule . DS . 'data' . DS,
            DIR_STORAGE . 'smb' . DS . $sModule . DS . 'module.smb'
        ), PCLZIP_OPT_REMOVE_PATH, DIR_STORAGE . 'smb' . DS . $sModule . DS);
        Dir::delete(DIR_STORAGE . 'smb' . DS . $sModule . DS);
        return (DIR_STORAGE . 'smb' . DS . $sModule . '.zip');
    }

    public function import($sFile)
    {
        // extract backup archive
        $oPcl = Linko::PclZip($sFile);
        $oPcl->extract(PCLZIP_OPT_PATH, DIR_TMP . 'smb' . DS);
        $sModule = trim(File::read(DIR_TMP . 'smb' . DS . 'module.smb'));

        if(empty($sModule))
        {
            $this->_cleanupImport();
            return array(false, "Invalid Backup File!");
        }

        // Install module if it does not exists
        if(!Linko::Model('Module')->isInstalled($sModule))
        {
            Dir::move(DIR_TMP . 'smb' . DS . $sModule, DIR_MODULE . $sModule);
            Linko::Model('Module/Action')->install($sModule);
        }

        if(Dir::exists(DIR_TMP . 'smb' . DS . 'data' . DS))
        {
            $aTables = Dir::read(DIR_TMP . 'smb' . DS . 'data' . DS);
            foreach($aTables as $sTable)
            {
                $aData = Linko::Xml()->parse($sTable);
                $aInsert = array();

                foreach($aData['row'] as $aRow)
                {
                    $aInsert[] = $aRow;
                }

                Linko::Database()->table($aData['name'])
                    ->insert($aInsert)
                    ->query();
            }
        }

        $this->_cleanupImport();
        return array(true, "Module Backup Imported Successfully!");
    }

    private function _cleanupImport()
    {
        Dir::delete(DIR_TMP . 'smb' . DS, true);
    }
}