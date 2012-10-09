<?php

class RetrieveTest extends BaseTest {
    
    public function setUp() {
        $this->setTestFixturePath(__CLASS__, $this->getName());
    }

    public function testRetrieveSitemapsOrgXmlSitemap() {        
        $sitemap = $this->createSitemap();        
        $sitemap->setUrl('http://webignition.net/sitemap.xml');
        $this->getSitemapRetriever()->retrieve($sitemap);
        
        $this->assertTrue($sitemap->isSitemap());
        $this->assertFalse($sitemap->isIndex());
        $this->assertEquals(10, count($sitemap->getUrls()));
    }
    
    public function testRetrieveSitemapsOrgTxtSitemap() {                
        $sitemap = $this->createSitemap();        
        $sitemap->setUrl('http://webignition.net/sitemap.txt');
        $this->getSitemapRetriever()->retrieve($sitemap);
        
        $this->assertTrue($sitemap->isSitemap());
        $this->assertFalse($sitemap->isIndex());
        $this->assertEquals(10, count($sitemap->getUrls()));
    }    
    
    
    public function testRetrieveAtomSitemap() {        
        $sitemap = $this->createSitemap();        
        $sitemap->setUrl('http://webignition.net/atom.xml');
        $this->getSitemapRetriever()->retrieve($sitemap);
        
        $this->assertTrue($sitemap->isSitemap());
        $this->assertFalse($sitemap->isIndex());
        $this->assertEquals(25, count($sitemap->getUrls()));
    }   
    
    
    public function testRetrieveRssSitemap() {                
        $sitemap = $this->createSitemap();        
        $sitemap->setUrl('http://webignition.net/rss.xml');
        $this->getSitemapRetriever()->retrieve($sitemap);
        
        $this->assertTrue($sitemap->isSitemap());
        $this->assertFalse($sitemap->isIndex());
        $this->assertEquals(3, count($sitemap->getUrls()));
    }      
    
}