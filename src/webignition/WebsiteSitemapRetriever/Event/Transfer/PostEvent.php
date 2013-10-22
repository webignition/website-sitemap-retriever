<?php
namespace webignition\WebsiteSitemapRetriever\Event\Transfer;

use \webignition\WebsiteSitemapRetriever\Event\Event as BaseEvent;
use webignition\WebsiteSitemapRetriever\WebsiteSitemapRetriever;

class PostEvent extends BaseEvent
{    
    /**
     *
     * @var PreEvent
     */
    private $preEvent = null;
    

    public function __construct(WebsiteSitemapRetriever $retriever, PreEvent $preEvent)
    {
        $this->preEvent = $preEvent;
        parent::__construct($retriever);
    }
    
    
    /**
     * 
     * @return PreEvent
     */
    public function getPreEvent() {
        return $this->preEvent;
    }
}