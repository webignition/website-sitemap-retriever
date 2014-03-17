<?php

namespace webignition\Tests\WebsiteSitemapRetriever\Retrieve\CookiesTest\SitemapIndex;

class DomainTest extends SitemapIndexTest { 
    
    protected function getSitemapUrl() {
        return 'http://example.com/sitemap.xml';
    }
    
    protected function getCookies() {
        return array(
            array(
                'domain' => '.example.com',
                'name' => 'foo',
                'value' => 'bar'
            )
        );
    }

    protected function getExpectedRequestsOnWhichCookiesShouldBeSet() {
        $requests = $this->getAllSentHttpRequests();
        
        return array(
            $requests[0],
            $requests[1],
        );
    }

    protected function getExpectedRequestsOnWhichCookiesShouldNotBeSet() {
        return array($this->getLastSentHttpRequest());
    }   
    
    
}