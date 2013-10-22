<?php

namespace webignition\WebsiteSitemapRetriever\Listener\Transfer;

use webignition\WebsiteSitemapRetriever\Event\Transfer\PostEvent;
use webignition\WebsiteSitemapRetriever\Event\Transfer\TotalTimeoutEvent;
use webignition\WebsiteSitemapRetriever\Events;

class PostEventListener {    

    public function onPostAction(PostEvent $event) {        
        $event->getRetriever()->appendTotalTransferTime(microtime(true) - $event->getPreEvent()->getStartTime());
        
        if ($event->getRetriever()->getTotalTransferTime() > $event->getRetriever()->getTotalTransferTimeout()) {            
            $totalTimeoutEvent = new TotalTimeoutEvent($event->getRetriever());
            
            $event->getDispatcher()->dispatch(Events::TRANSFER_TOTAL_TIMEOUT, $totalTimeoutEvent);
        }
    }

}