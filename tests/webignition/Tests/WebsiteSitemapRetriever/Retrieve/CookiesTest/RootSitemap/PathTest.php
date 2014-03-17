<?php

namespace webignition\Tests\WebsiteSitemapRetriever\Retrieve\CookiesTest\RootSitemap;

use webignition\Tests\WebsiteSitemapRetriever\Retrieve\CookiesTest\RootSitemap\RootSitemapTest;

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
        return array($this->getHttpHistory()->getLastRequest());
    }

    protected function getExpectedRequestsOnWhichCookiesShouldNotBeSet() {
        return array();
    }    
}