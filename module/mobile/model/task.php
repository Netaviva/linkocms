<?php defined('Linko') or exit;

/**
 * @package Mobile Module
 * @author Morrison Laju <morrelinko@gmail.com>
 */
class Mobile_Model_Task extends Linko_Model
{
    public function module_install()
    {
        // Creates a 'mobile' folder in
        // %root%/storage/upload/
        // When this module is installed.
        if(!Dir::exists(DIR_UPLOAD . 'mobile'))
        {
            Dir::create(DIR_UPLOAD . 'mobile');
        }
    }

    public function module_uninstall()
    {
        Dir::delete(DIR_UPLOAD . 'mobile');
    }
}