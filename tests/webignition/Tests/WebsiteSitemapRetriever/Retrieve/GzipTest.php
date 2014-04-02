<?php

namespace webignition\Tests\WebsiteSitemapRetriever\Retrieve;

use webignition\Tests\WebsiteSitemapRetriever\BaseTest;

class GzipTest extends BaseTest {
    
    public function setUp() { 
        parent::setUp();
        $this->setHttpFixtures($this->getHttpFixtures($this->getFixturesDataPath() . '/HttpResponses'));
    }
    
    public function testRetrieveGzippedSitemap() {
        $sitemap = $this->createSitemap();        
        $sitemap->setUrl('http://worldclassmedia.com/sitemap_addl.xml.gz');
        $this->getSitemapRetriever()->retrieve($sitemap);
        
        $this->assertTrue($sitemap->isSitemap());
        $this->assertFalse($sitemap->isIndex());
        $this->assertEquals(1, count($sitemap->getUrls()));
    }   
    
}