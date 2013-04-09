<?php

class LimitRetrievedUrlCountTest extends BaseTest {
    
    public function setUp() {
        $this->setHttpFixtures($this->getHttpFixtures($this->getFixturesDataPath(__CLASS__, $this->getName() . '/HttpResponses')));
    }

    public function testLimitOnRegularSitemap() {
        $sitemap = $this->createSitemap();        
        $sitemap->setUrl('http://webignition.net/sitemap.xml');
        $this->getSitemapRetriever()->enableLimitRetrievedUrlCount();
        $this->getSitemapRetriever()->setRetrievedUrlCountThreshold(5);        
        $this->getSitemapRetriever()->retrieve($sitemap);
        
        $this->assertTrue($sitemap->isSitemap());
        $this->assertFalse($sitemap->isIndex());
        $this->assertEquals(10, count($sitemap->getUrls()));        
    }
    
    public function testLimitOnIndexSitemap() {       
        $sitemap = $this->createSitemap();
        $sitemap->setUrl('http://io9.com/sitemap.xml');
        $this->getSitemapRetriever()->enableLimitRetrievedUrlCount();
        $this->getSitemapRetriever()->setRetrievedUrlCountThreshold(50);          
        $this->getSitemapRetriever()->retrieve($sitemap);      
        
        $this->assertTrue($sitemap->isSitemap());
        $this->assertTrue($sitemap->isIndex());
        $this->assertEquals(5, count($sitemap->getUrls()));
        $this->assertEquals(5, count($sitemap->getChildren()));
      
        $urls = array();
        foreach ($sitemap->getChildren() as $childSitemapIndex => $childSitemap) {                        
            $urls = array_merge($urls, $childSitemap->getUrls());
        }
        
        $this->assertEquals(87, count($urls));
    }
    
}