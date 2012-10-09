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
    
    public function testRetrieveSitemapOrgXmlIndex() {       
        $sitemap = $this->createSitemap();
        $sitemap->setUrl('http://io9.com/sitemap.xml');
        $this->getSitemapRetriever()->retrieve($sitemap);
        
        $this->assertTrue($sitemap->isSitemap());
        $this->assertTrue($sitemap->isIndex());
        $this->assertEquals(5, count($sitemap->getUrls()));
        $this->assertEquals(5, count($sitemap->getChildren()));
        
        $urlCount = 0;
        $childSitemapUrlCounts = array(
            '4110f998d6537492115c96745a715ecb' => 22,
            'b35b75d56d2bf76f71af9cc436488e07' => 76,
            '82d37105b3212f275819bfac6832f363' => 62,
            'a334b66e1d1d1b148b81c36fa676f11c' => 101,
            '2b3b74da7ff3c6d953aef56becd98cea' => 101
        );        
        
        $urls = array();
        foreach ($sitemap->getChildren() as $childSitemapIndex => $childSitemap) {
            $urls = array_merge($urls, $childSitemap->getUrls());
            $urlCount += $childSitemapUrlCounts[$childSitemapIndex];
            $this->assertEquals($childSitemapUrlCounts[$childSitemapIndex], count($childSitemap->getUrls()));
        }
        
        $this->assertEquals($urlCount, count($urls));
    }
    
    public function testShallowRetrieveSitemapOrgXmlIndex() {
        $sitemap = $this->createSitemap();
        $sitemap->setUrl('http://io9.com/sitemap.xml');
        
        $this->getSitemapRetriever()->disableRetrieveChildSitemaps();
        $this->getSitemapRetriever()->retrieve($sitemap);        
        
        $this->assertTrue($sitemap->isSitemap());
        $this->assertTrue($sitemap->isIndex());
        $this->assertEquals(5, count($sitemap->getUrls()));
        $this->assertEquals(0, count($sitemap->getChildren()));
    }
    
    
}