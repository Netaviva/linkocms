<?php

defined('LINKO') or exit();

/**
 * @author Morrison Laju <morrelinko@gmail.com>
 * @package linkocms
 */
class Smb_Plugin_Admincp
{
    public function init()
    {
        Linko::Model('Admincp')->addRoute('smb(/[archive].zip)', array(
            'id' => 'smb:admincp',
            'controller' => 'smb/admincp/index',
            'rule' => array(
                'archive' => ':alnum'
            )
        ));

        Linko::Model('Admincp')->addRoute('smb/import', array(
            'id' => 'smb:admincp:import',
            'controller' => 'smb/admincp/import'
        ));

        Linko::Model('Admincp')->addMenu('Super Module Backup', array(
            'Import Backup' => Linko::Url()->make('smb:admincp:import'),
            'Manage Backups' => Linko::Url()->make('smb:admincp')
        ));
    }
}