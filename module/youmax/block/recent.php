<?php

class Tweets_Block_Recent extends Linko_Controller
{
    public function main()
    {
        $iUsername = Linko::Module()->getSetting('tweets.username');
        
        $iLimit = (int)Linko::Module()->getSetting('tweets.number_of_tweets');
        
        $aTweets = Linko::Model('Tweets')->getTweets($iUsername, $iLimit, $bParse = true);
        
        Linko::Template()->setVars(array(
            'username' => $iUsername,
            'tweets' => $aTweets,
        ));
    }
}

?>