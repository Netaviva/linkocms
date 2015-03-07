<?php

class Messages_Plugin_Messages
{
    public function notify()
    {
        echo 'twalo';
        Linko::Template()->setStyle('twalo.css','module_messges');
        return  'twalo in th';
    }
}