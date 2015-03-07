<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV Team
 * @package linkocms
 * @subpackage wysiwyg : model - wysiwyg.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */
class Editor_Model_Editor extends Linko_Model
{
    public function set($sId, $aParams = array())
    {
        extract(array_merge(array(
            'width' => '100%',
            'height' => '270px',
            'align' => 'middle',
        ), $aParams));
    
        Linko::Template()
            ->setScript('editors/tiny_mce/tiny_mce.js', 'module_editor')
            ->setFooter($this->_setParam($sId, array(
                'width' => $width,
                'mode' => 'specific_textareas',
                'skin' => 'default',
                'theme' => 'advanced',
                'editor_selector' => $sId,
                'document_base_url' => Linko::Url()->make(),

                'invalid_elements' => 'script,applet,iframe',
                
          		'plugins' => 'autolink,lists,style,layer,table,advhr,advimage,advlink,inlinepopups,insertdatetime,preview,media,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist,autosave,visualblocks',
                
                'theme_advanced_buttons1' => 'save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect',
                'theme_advanced_buttons2' => 'cut,copy,paste,pastetext,pasteword,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,code,|,insertdate,inserttime,preview,|,forecolor,backcolor',

                'theme_advanced_toolbar_location' => 'top',
				'theme_advanced_source_editor_height' => "550",
				'theme_advanced_source_editor_width' => "750",
				'theme_advanced_resizing' => true,
				'theme_advanced_resize_horizontal' => false,
                'theme_advanced_resizing_use_cookie' => false,
				'theme_advanced_statusbar_location' => "bottom",
                'theme_advanced_path' => true,

                // Style formats
                "style_formats" => array(
                    array('title' => 'Bold text', 'inline' => 'b')
                ),
            )));
    }
    
    private function _setParam($sId, $aParam)
    {
        $sJs = Linko::Json()->encode($aParam, Linko_Json::JAVASCRIPT_FORMAT);
        
        return Html::script("
            tinyMCE.init(" . $sJs . ");
        ");
    }
}
?>