<?php

namespace webignition\WebsiteSitemapRetriever;

/**
 * Documents that events that can occur
 */
final class Events {
    
    /**
     * The transfer.pre event is thrown prior to initiating a HTTP transfer
     * to retrieve a sitemap.
     * 
     * @var string
     */    
    const TRANSFER_PRE = 'transfer.pre';
    
    /**
     * The transfer.pre event is thrown after completing a HTTP transfer
     * to retrieve a sitemap.
     * 
     * @var string
     */    
    const TRANSFER_POST = 'transfer.post';    
    
    
    /**
     * The transfer.totaltimeout event is thrown if the total transfer time
     * for the sitemap (covering parent and children where relevant) exceeds
     * the given limit.
     * 
     * @var string
     */    
    const TRANSFER_TOTAL_TIMEOUT = 'transfer.totaltimeout';       
}