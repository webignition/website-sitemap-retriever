<?php
namespace webignition\WebsiteSitemapRetriever\Event\Transfer;

use \webignition\WebsiteSitemapRetriever\Event\Event as BaseEvent;

class PreEvent extends BaseEvent
{    
    /**
     *
     * @var float
     */
    private $startTime = 0;
    
    
    /**
     * 
     * @param float $startTime
     */
    public function setStartTime($startTime) {
        $this->startTime = $startTime;
    }
    
    
    /**
     * 
     * @return float
     */
    public function getStartTime() {
        return $this->startTime;
    }
}