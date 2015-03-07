<?php

class Assetic_Model_Task extends Linko_Model
{
    public function module_install()
    {
        if(!Dir::exists(DIR_TMP . 'assetic'))
        {
            Dir::create(DIR_TMP . 'assetic');
        }
    }
}