<?php

defined('LINKO') or exit();

/**
 * @author LinkoDEV team
 * @package linkocms
 * @subpackage comment : process.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All rights reserved.
 */
class Comment_Controller_Process extends Linko_Controller
{
    public function main()
    {
        if($aVals = Input::post('val'))
        {
            if(!isset($aVals['return_to']))
            {
                $aVals['return_to'] = (($sReferer = Linko::Request()->getReferer()) && ($sReferer != null)) ? $sReferer : Linko::Url()->make();
            }

            if($this->getSetting('comment.members_only_comment') && !Linko::Model('User/Auth')->isUser())
            {
                Linko::Flash()->warning(Lang::t('comment.you_cannot_post_comment'));
                Linko::Response()->redirect($aVals['return_to']);
            }

            // these must exists in the form
            if(!Arr::hasKeys($aVals, 'item_id', 'module_id', 'comment'))
            {
                Linko::Flash()->warning(Lang::t('comment.invalid_post_data'));
                Linko::Response()->redirect($aVals['return_to']);
                
                return;
            }

            $aValidate = array();
            if(!Linko::Model('User/Auth')->isUser())
            {
                $aValidate['name'] = array(
                    'function' => 'length:2,',
                    'error' => Lang::t('comment.your_name_is_required')
                );

                $aValidate['email'] = array(
                    'function' => 'email',
                    'error' => Lang::t('comment.invalid_email')
                );
            }

            $aValidate['comment'] = array(
                'function' => 'length:2,',
                'error' => Lang::t('comment.your_comment_is_short')
            );

            Linko::Validate()->set($aValidate);

            if(Linko::Validate()->isValid($aVals))
            {
                if(Linko::Model('Comment/Action')->addComment($aVals))
                {
                    Linko::Flash()->success(Lang::t('comment.your_comment_has_been_posted'));
                    Linko::Response()->redirect($aVals['return_to']); 
                    
                    return;  
                }
            }
        }
        
        Linko::Flash()->warning(Lang::t('comment.error_posting_comment') . "<br />" . implode("<br />", Linko::Error()->get()));

        Linko::Response()->redirect($aVals['return_to']);

        // no layout
        Linko::Template()->displayLayout(false);
    }
}

?>