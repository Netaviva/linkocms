<?php

defined('LINKO') or exit;

class Template_Plugin_Comment
{
    public function start($aParams = array())
    {
        /**
         * @var $module_id
         * @var $item_id
         * @var $comment_form
         * @var $comment_list_header
         * @var $comment_form_header
         */
        if(!Arr::hasKeys($aParams, 'module_id', 'item_id'))
        {
            return;
        }

        extract(array_merge(array(
            'comment_form' => true,
            'comment_list' => true,
            'comment_list_header' => 'Comments',
            'comment_form_header' => 'Leave a Reply',
        ), $aParams));

        /**
         * <div id="comments">
         *  <h4> Comments </h4>
         *  ... Comment list
         * </div>
         *
         * <div id="comments-form">
         *  <h4>Leave a Reply <small style="display: none;">Replying to comment</small></h4>
         *  ... Comment Form
         * </div>
         */
        $sHtml = Html::openTag('div', array('id' => 'comments'));
        $sHtml .= Html::tag('h4', $comment_list_header);
        $sHtml .= Linko::Module()->getBlock('comment/display', array('module_id' => $module_id, 'item_id' => $item_id));
        $sHtml .= Html::closeTag('div');

        if($comment_form)
        {
            $sType = gettype($comment_form);

            $sHtml .= Html::openTag('div', array('id' => 'comments-form'));

            if($sType == 'boolean')
            {

                $sHtml .= Html::tag('h4', $comment_form_header . Html::tag('small', Lang::t('comment.replying_to_comment'), array('style' => 'display: none;')));
                $sHtml .= Linko::Module()->getBlock('comment/form', array('sModule' => $module_id, 'iItemId' => $item_id));
            }
            else
            {
                $sHtml .= $comment_form;
            }

            $sHtml .= Html::closeTag('div');
        }

        echo $sHtml;
    }
}

?>