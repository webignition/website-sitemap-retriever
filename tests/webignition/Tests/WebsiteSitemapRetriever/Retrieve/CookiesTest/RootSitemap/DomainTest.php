<?php

namespace webignition\Tests\WebsiteSitemapRetriever\Retrieve\CookiesTest\RootSitemap;

class DomainTest extends RootSitemapTest { 
    
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
        return array($this->getLastSentHttpRequest());
    }

    protected function getExpectedRequestsOnWhichCookiesShouldNotBeSet() {
        return array();
    }    
}