<?php

namespace webignition\WebsiteSitemapRetriever\Listener\Transfer;

use webignition\WebsiteSitemapRetriever\Event\Transfer\TotalTimeoutEvent;

class TotalTimeoutEventListener {    

    public function onTimeoutAction(TotalTimeoutEvent $event) {
        $event->getRetriever()->enableShouldHalt();
    }

}