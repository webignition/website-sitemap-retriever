<?php

namespace webignition\Tests\WebsiteSitemapRetriever;

class RetrieveTest extends BaseTest {
    
    public function setUp() { 
        parent::setUp();
        $this->setHttpFixtures($this->getHttpFixtures($this->getFixturesDataPath() . '/HttpResponses'));
    }

    public function testRetrieveSitemapsOrgXmlSitemap() {
        $sitemap = $this->createSitemap();        
        $sitemap->setUrl('http://example.com/sitemap.xml');
        $this->getSitemapRetriever()->retrieve($sitemap);
        
        $this->assertTrue($sitemap->isSitemap());
        $this->assertFalse($sitemap->isIndex());
        $this->assertEquals(10, count($sitemap->getUrls()));
    }
    
    public function testRetrieveSitemapsOrgTxtSitemap() {                
        $sitemap = $this->createSitemap();        
        $sitemap->setUrl('http://example.com/sitemap.txt');
        $this->getSitemapRetriever()->retrieve($sitemap);
        
        $this->assertTrue($sitemap->isSitemap());
        $this->assertFalse($sitemap->isIndex());
        $this->assertEquals(10, count($sitemap->getUrls()));
    }    
    
    
    public function testRetrieveAtomSitemap() {        
        $sitemap = $this->createSitemap();        
        $sitemap->setUrl('http://example.com/atom.xml');
        $this->getSitemapRetriever()->retrieve($sitemap);
        
        $this->assertTrue($sitemap->isSitemap());
        $this->assertFalse($sitemap->isIndex());
        $this->assertEquals(25, count($sitemap->getUrls()));
    }   
    
    
    public function testRetrieveRssSitemap() {                
        $sitemap = $this->createSitemap();        
        $sitemap->setUrl('http://example.com/rss.xml');
        $this->getSitemapRetriever()->retrieve($sitemap);
        
        $this->assertTrue($sitemap->isSitemap());
        $this->assertFalse($sitemap->isIndex());
        $this->assertEquals(3, count($sitemap->getUrls()));
    }
    
    public function testRetrieveSitemapOrgXmlIndex() {       
        $sitemap = $this->createSitemap();
        $sitemap->setUrl('http://example.com/sitemap.xml');
        $this->getSitemapRetriever()->retrieve($sitemap);
        
        $this->assertTrue($sitemap->isSitemap());
        $this->assertTrue($sitemap->isIndex());
        $this->assertEquals(3, count($sitemap->getUrls()));
        $this->assertEquals(3, count($sitemap->getChildren()));
        
        $urlCount = 0;
        $childSitemapUrlCounts = array(
            '4110f998d6537492115c96745a715ecb' => 16,
            'b35b75d56d2bf76f71af9cc436488e07' => 71,
            '3cf7b1bfdf1988dff5b951904deb4139' => 67
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
        $sitemap->setUrl('http://example.com/sitemap.xml');
        
        $this->getSitemapRetriever()->disableRetrieveChildSitemaps();
        $this->getSitemapRetriever()->retrieve($sitemap);        
        
        $this->assertTrue($sitemap->isSitemap());
        $this->assertTrue($sitemap->isIndex());
        $this->assertEquals(5, count($sitemap->getUrls()));
        $this->assertEquals(5, count($sitemap->getChildren()));              
        
        foreach ($sitemap->getChildren() as $childSitemap) {
            $this->assertNull($childSitemap->getContent());
        }
    }
    
    public function testTotalTransferTimeout() {
        $sitemap = $this->createSitemap();
        $sitemap->setUrl('http://example.com/sitemap_index.xml');

        $this->getSitemapRetriever()->setTotalTransferTimeout(0.0001);
        $this->getSitemapRetriever()->retrieve($sitemap);        
        
        $this->assertEquals(2, count($sitemap->getChildren()));
        
        foreach ($sitemap->getChildren() as $index => $childSitemap) {
            if ($index === 0) {
                $this->assertEquals(2, count($childSitemap->getUrls()));
            } else {
                $this->assertEquals(0, count($childSitemap->getUrls()));
            }
        }        
    }
    
    public function testRetrieveHttpAuthProtectedSitemap() {
        $sitemap = $this->createSitemap();
        $sitemap->setUrl('http://http-auth-01.simplytestable.com/sitemap.xml');

        $this->getSitemapRetriever()->getBaseRequest()->setAuth('example', 'password', 'any');
        $this->getSitemapRetriever()->retrieve($sitemap);        
        
        $this->assertEquals(array(
            'http://http-auth-01.simplytestable.com/index.html'
        ), $sitemap->getUrls());
    }    
    
}