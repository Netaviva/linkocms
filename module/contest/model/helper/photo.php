<?php

class Contest_Model_Helper_Photo extends Linko_Model
{
    public function getPhoto($aParams)
    {
        //arr::dump($aParams);
        /**
         * @var $user
         * @var $size
         */
        extract(array_merge(array(
            'post' => array(),
            'size' => 200,
        ), $aParams));

        //arr::dump($post);
        $sPath = (isset($post['post_image']) && $post['post_image'] != null) ? $post['post_image'] : 'no_photo_%d.png';

        //echo $sPath;
        $sUrl = Linko::Url()->path('storage/upload/contest') . sprintf($sPath, $size);

        return $sUrl;
    }
}