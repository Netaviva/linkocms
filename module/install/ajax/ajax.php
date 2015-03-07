<?php

class Install_Ajax extends Linko_Ajax
{
    public function chmod()
    {
        if(Linko::Model('Install')->isInstalled())
        {
            return false;
        }

        $sFile = $this->getParam('file');

        $bRet = @chmod($sFile, 777);

        $this->toJson(array(
            'return' => $bRet
        ));
    }
}