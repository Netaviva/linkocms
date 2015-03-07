<?php

class Tweets_Block_Recent extends Linko_Controller
{
    public function main()
    {
       $iUsername = $this->getParam('tweetable.username');
       $iLimit = $this->getParam('tweetable.number_of_tweets');
        Linko::Template()
                ->setStyle('twitterwidget.css', 'module_tweets', 'header')
                ->setScript(array('tweetable.jquery.min.js','jquery.timeago.js'), 'module_tweets', 'footer')
                ->setVars(array(
            'username' => $iUsername,
            'limit' => $iLimit,
        ));
    }
}

?>