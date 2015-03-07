<?php

class Assetic_Controller_Load_Asset extends Linko_Controller
{
    public function main()
    {
        $sType = $this->getParam('type');
        $sCache = $this->getParam('cache');

        $sContent = null;

        if(File::exists(ASSETIC_DIR . $sCache))
        {
            $sContent = File::read(ASSETIC_DIR . $sCache);

            switch($sType)
            {
                case 'script':
                    Linko::Response()->setHeaders(array('Content-type' => 'text/javascript'));
                    break;
                case 'style':
                    Linko::Response()->setHeaders(array('Content-type' => 'text/css'));
                    break;
            }

            Linko::Response()->setStatus(200);
            Linko::Response()->setData($sContent);
        }

        Linko::Template()->displayLayout(false);
    }
}