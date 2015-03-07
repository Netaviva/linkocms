<?php

class Assetic_Plugin_Application
{
    public function before_layout()
    {
        // Get styles (header & footer)
        // @todo Remove styles not from this domain
        $aHeaderStyle = Linko::Template()->getStyle('header', true);
        $aFooterStyle = Linko::Template()->getStyle('footer', true); // Ooops! Stylesheets in footer?!

        Linko::Template()->clearStyle();

        // generate hash cache styles
        $sHeaderStyleHash = 'assetic_' . md5('style_header_' . serialize($aHeaderStyle));
        $sFooterStyleHash = 'assetic_' . md5('style_footer_' . serialize($aFooterStyle));

        // style header
        if(!File::exists(ASSETIC_DIR . $sHeaderStyleHash) || true)
        {
            $sContent = null;

            foreach($aHeaderStyle as $sHeaderStyle)
            {
                $sFile = realpath(str_replace(array(Linko::Url()->path(), '/'), array('', DS), $sHeaderStyle));

                $sContent .= "\n/** File: " . $sFile . " */\n";
                $sContent .= File::read($sFile);
            }

            File::write(ASSETIC_DIR . $sHeaderStyleHash, $sContent, null, true);
        }

        $sHeaderStyleUrl = Linko::Url()->make('assetic:url', array(
            'type' => 'style',
            'cache' => $sHeaderStyleHash,
            'extension' => 'css'
        ));

        Linko::Template()->setStyle($sHeaderStyleUrl, null, 'header');

        // Get scripts (header & footer)
        // @todo Remove scripts not from this domain
        $aHeaderScript = Linko::Template()->getScript('header', true);
        $aFooterScript = Linko::Template()->getScript('footer', true);

        Linko::Template()->clearScript();

        // generate hash to cache scripts
        $sHeaderScriptHash = 'assetic_' . md5('script_header_' . serialize($aHeaderScript));
        $sFooterScriptHash = 'assetic_' . md5('script_footer_' . serialize($aFooterScript));

        // script header
        if(!File::exists(ASSETIC_DIR . $sHeaderScriptHash) || true)
        {
            $sContent = null;

            foreach($aHeaderScript as $sHeaderScript)
            {
                $sFile = realpath(str_replace(array(Linko::Url()->path(), '/'), array('', DS), $sHeaderScript));

                $sContent .= "\n/** File: " . $sFile . " */\n";
                $sContent .= File::read($sFile);
            }

            File::write(ASSETIC_DIR . $sHeaderScriptHash, $sContent, null, true);
        }

        if(!File::exists(ASSETIC_DIR . $sFooterScriptHash))
        {
            File::write(ASSETIC_DIR . $sFooterScriptHash, '/** Generated By Assetic */', null, true);
        }

        $sHeaderScriptUrl = Linko::Url()->make('assetic:url', array(
            'type' => 'script',
            'cache' => $sHeaderScriptHash,
            'extension' => 'js'
        ));

        $sFooterScriptUrl = Linko::Url()->make('assetic:url', array(
            'type' => 'script',
            'cache' => $sFooterScriptHash,
            'extension' => 'js'
        ));

        Linko::Template()->setScript($sHeaderScriptUrl, null, 'header');
        Linko::Template()->setScript($sFooterScriptUrl, null, 'footer');
    }
}

?>