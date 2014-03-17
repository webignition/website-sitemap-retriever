<?php

namespace webignition\Tests\WebsiteSitemapRetriever\Retrieve\CookiesTest\SitemapIndex;

class PathTest extends SitemapIndexTest { 
    
    protected function getSitemapUrl() {
        return 'http://example.com/foo/sitemap.xml';
    }
    
    protected function getCookies() {
        return array(
            array(
                'domain' => '.example.com',
                'path' => '/foo',
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