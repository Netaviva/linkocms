<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV Team
 * @package linkocms
 * @subpackage shortcode : loader
 * @version 1.0.0
 * @copyright Netaviva (c) 2013. All Rights Reserved.
 */
class Mod_Shortcodes {

    public function add() {
        Linko::Shortcode()->add('b', array(&$this, 'bold'));
        Linko::Shortcode()->add('u', array(&$this, 'underline'));
        Linko::Shortcode()->add('i', array(&$this, 'italic'));
        Linko::Shortcode()->add('noparse', array(&$this, 'noParse'));
        Linko::Shortcode()->add('code', array(&$this, 'syntax'));
        Linko::Shortcode()->add('youtube', array(&$this, 'youtube'));
        Linko::Shortcode()->add('url', array(&$this, 'url'));
        Linko::Shortcode()->add('br', array(&$this, 'br'));
        Linko::Shortcode()->add('google_map', array(&$this, 'google_map'));
    }

    public function noParse($content) {
        return str_replace(array('[', ']'), array('[', ']'), $content);
    }

    public function br() {
        return '<br />';
    }

    public function bold($content) {
        return '<b>' . $content . '</b>';
    }

    public function underline($content) {
        return '<u>' . $content . '</u>';
    }

    public function italic($content) {
        return '<i>' . $content . '</i>';
    }

    public function syntax($content, $attr) {
        extract(array_merge(array(
                    'type' => ''
                        ), $attr));

        $type = strtolower($type);

        if ($type == 'php') {
            return highlight_string($content, true);
        } else {
            return htmlspecialchars($content);
        }
    }

    public function youtube($content, $attr) {
        extract(array_merge(array(
                    'id' => ''
                        ), $attr));

        return '<object type="application/x-shockwave-flash" style="width: 450px; height: 366px;" data="http://www.youtube.com/v/' . $id . '">
			<param name="movie" value="http://www.youtube.com/v/' . $id . '" />
			<param name="wmode" value="transparent" />		
		</object>';
    }

    public function url($content, $attr) {
        extract(array_merge(array(
                    'link' => ''
                        ), $attr));

        unset($attr['link']);

        return Html::link($content, Linko::Url()->make($link, $attr));
    }

    // Google Map //
    public function google_map($content = null, $attr) {
        extract(array_merge(array(
                    "width" => '640',
                    "height" => '480',
                    "src" => ''
                        ), $attr));

        return '<iframe width="' . $width . '" height="' . $height . '" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="' . $src . '"></iframe>';
    }

}

$oSc = new Mod_Shortcodes;

$oSc->add();
?>