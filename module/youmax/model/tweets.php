<?php

class Tweets_Model_Tweets extends Linko_Model {

    private $feed_url = 'http://api.twitter.com/1/statuses/user_timeline.json?trim_user=1&include_rts=1';

    public function main() {
        
    }

    public function getTweets($iUsername, $iLimit, $bParse = true) {
        
        Linko::Cache()->set(array('tweets', 'twitter_' . $iUsername . '_' . ($bParse ? 'parsed' : 'original')));
        
        if (!$tweets = Linko::Cache()->read()){
            $tweets = json_decode(@file_get_contents($this->feed_url . '&screen_name=' . $iUsername . '&count=' . $iLimit));

            Linko::Cache()->write($tweets);
        }

        $patterns = array(
            // Detect URL's
            '((https?|ftp|gopher|telnet|file|notes|ms-help):((//)|(\\\\))+[\w\d:#@%/;$()~_?\+-=\\\.&]*)' => '<a href="$0" target="_blank">$0</a>',
            // Detect Email
            '|([a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,6})|i' => '<a href="mailto:$1">$1</a>',
            // Detect Twitter @usernames
            '| @([a-z0-9-_]+)|i' => '<a href="http://twitter.com/$1" target="_blank">$0</a>',
            // Detect Twitter #tags
            '|#([a-z0-9-_]+)|i' => '<a href="http://twitter.com/search?q=%23$1" target="_blank">$0</a>'
        );   
     
        if ($tweets) {
            foreach ($tweets as &$tweet) {

                $tweet->text = str_replace($iUsername . ': ', '', $tweet->text);
                $tweet->text = preg_replace(array_keys($patterns), $patterns, $tweet->text);
               // var_dump($tweet->text);
            }
        }
        
        return $tweets;
      
    }

}

?>