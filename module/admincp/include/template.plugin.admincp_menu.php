<?php

class Template_Plugin_Admincp_Menu
{
    public function start($aParam = array())
    {
        /**
         * @var array $categories menu categories to load
         */
        extract(array_merge(array(
            'categories' => 'main,required_modules'
        )), $aParam);

        $categories = explode(',', $categories);
    }
}

?>