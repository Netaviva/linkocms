<?php

class Blog_Model_Helper_Photo extends Linko_Model
{
    public function getPhoto($aParams)
    {
        /**
         * @var $user
         * @var $size
         */
        extract(array_merge(array(
            'post' => array(),
            'size' => 500,
        ), $aParams));

        $sPath = (isset($post['post_image']) && $post['post_image'] != null) ? $post['post_image'] : 'no_photo_%d.png';
      
        $sUrl = Linko::Url()->path('storage/upload/blog') . sprintf($sPath, $size);

        return $sUrl;
    }
}