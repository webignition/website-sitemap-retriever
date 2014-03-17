<?php

namespace webignition\Tests\WebsiteSitemapRetriever\Retrieve\CookiesTest\SitemapIndex;

class SecureTest extends SitemapIndexTest { 
    
    protected function getSitemapUrl() {
        return 'https://example.com/sitemap.xml';
    }    
    
    protected function getCookies() {
        return array(
            array(
                'domain' => '.example.com',
                'secure' => true,
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