<?php
namespace webignition\WebsiteSitemapRetriever\Event;

use Symfony\Component\EventDispatcher\Event as BaseEvent;
use webignition\WebsiteSitemapRetriever\WebsiteSitemapRetriever;

abstract class Event extends BaseEvent
{
    /**
     *
     * @var \webignition\WebsiteSitemapRetriever\WebsiteSitemapRetriever 
     */
    private $retriever = null;
    

    /**
     * 
     * @param \webignition\WebsiteSitemapRetriever\WebsiteSitemapRetriever $retriever
     */
    public function __construct(WebsiteSitemapRetriever $retriever)
    {
        $this->retriever = $retriever;
    }
    
    
    
    /**
     * 
     * @return \webignition\WebsiteSitemapRetriever\WebsiteSitemapRetriever
     */
    public function getRetriever() {
        return $this->retriever;
    }    
}