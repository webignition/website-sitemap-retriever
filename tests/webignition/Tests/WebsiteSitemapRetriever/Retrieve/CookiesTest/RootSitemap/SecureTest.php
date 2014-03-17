<?php

namespace webignition\Tests\WebsiteSitemapRetriever\Retrieve\CookiesTest\RootSitemap;

use webignition\Tests\WebsiteSitemapRetriever\Retrieve\CookiesTest\RootSitemap\RootSitemapTest;

class SecureTest extends RootSitemapTest { 
    
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
        return array($this->getHttpHistory()->getLastRequest());
    }

    protected function getExpectedRequestsOnWhichCookiesShouldNotBeSet() {
        return array();
    }    
}