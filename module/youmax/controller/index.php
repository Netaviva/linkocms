<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV team
 * @package linkocms
 * @subpackage youmax : index.php
 * @version 1.0
 * @copyright Netaviva (c) 2013. All rights reserved.
 */
class Youmax_Controller_index extends Linko_Controller {

    public function main() {

        $sYoutubeChannelURL = Linko::Module()->getSetting('youmax.youTubeChannelURL');

        $sYoutubePlaylistURL = Linko::Module()->getSetting('youmax.youTubePlaylistURL');

        $sYoutubeDefaultTab = Linko::Module()->getSetting('youmax.youmaxDefaultTab');

        Linko::Template()
                ->setTitle(Lang::t('youmax.video_gallery'))
                ->setScript('youmax.min.js', 'module_youmax', 'footer')
                ->setVars(
                        array(
                            'youtubeChannelURL' => $sYoutubeChannelURL,
                            'youtubePlaylistURL' => $sYoutubePlaylistURL,
                            'youtubeDefaultTab' => $sYoutubeDefaultTab
                ));
    }

}

?>