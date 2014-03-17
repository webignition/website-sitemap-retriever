<?php

namespace webignition\Tests\WebsiteSitemapRetriever\Retrieve\CookiesTest\RootSitemap;

class PathTest extends RootSitemapTest { 
    
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
        return array($this->getLastSentHttpRequest());
    }

    protected function getExpectedRequestsOnWhichCookiesShouldNotBeSet() {
        return array();
    }    
}