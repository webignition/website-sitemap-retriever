<?php

namespace webignition\WebsiteSitemapRetriever\Listener\Transfer;

use webignition\WebsiteSitemapRetriever\Event\Transfer\PreEvent;

class PreEventListener {    

    public function onPreAction(PreEvent $event) {
        $event->setStartTime(microtime(true));
    }

}