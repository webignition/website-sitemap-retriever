<?php

namespace webignition\Tests\WebsiteSitemapRetriever\Retrieve\CookiesTest;

use webignition\Tests\WebsiteSitemapRetriever\BaseTest;

abstract class CookiesTest extends BaseTest {
    
    /**
     * 
     * @return array
     */
    abstract protected function getCookies();
    
    /**
     * 
     * @return \Guzzle\Http\Message\RequestInterface[]
     */    
    abstract protected function getExpectedRequestsOnWhichCookiesShouldBeSet();
    
    
    /**
     * @return string
     */
    abstract protected function getSitemapUrl();
    
    
    /**
     * 
     * @return \Guzzle\Http\Message\RequestInterface[]
     */    
    abstract protected function getExpectedRequestsOnWhichCookiesShouldNotBeSet();    
    
    public function setUp() { 
        parent::setUp();
        $this->setHttpFixtures($this->getHttpFixtures($this->getFixturesDataPath() . '/HttpResponses'));
        
        $sitemap = $this->createSitemap();        
        $sitemap->setUrl($this->getSitemapUrl());
        
        $this->getSitemapRetriever()->getConfiguration()->setCookies($this->getCookies());
        $this->getSitemapRetriever()->retrieve($sitemap);  
    }    
    
    public function testCookiesAreSetOnExpectedRequests() {        
        foreach ($this->getExpectedRequestsOnWhichCookiesShouldBeSet() as $request) {                        
            $this->assertEquals($this->getExpectedCookieValues(), $request->getCookies());
        }
    }
    
    
    public function testCookiesAreNotSetOnExpectedRequests() {        
        foreach ($this->getExpectedRequestsOnWhichCookiesShouldNotBeSet() as $request) {            
            $this->assertEquals(array(), $request->getCookies());
        }
    }    
    

    /**
     * 
     * @return array
     */
    private function getExpectedCookieValues() {
        $nameValueArray = array();
        
        foreach ($this->getCookies() as $cookie) {
            $nameValueArray[$cookie['name']] = $cookie['value'];
        }
        
        return $nameValueArray;
    } 
    
    
    /**
     * 
     * @return \Guzzle\Http\Message\Request[]
     */
    protected function getAllSentHttpRequests() {
        $requests = array();
        
        foreach ($this->getHttpHistory()->getAll() as $httpTransaction) {
            $requests[] = $httpTransaction['request'];
        }
        
        return $requests;
    }

    
    /**
     * 
     * @return \Guzzle\Http\Message\Request
     */
    protected function getFirstSentHttpRequest() {
        $requests = $this->getAllSentHttpRequests();
        return $requests[0];
    }
    
    
    /**
     * 
     * @return \Guzzle\Http\Message\Request
     */
    protected function getLastSentHttpRequest() {
        $requests = $this->getAllSentHttpRequests();
        return $requests[count($requests) - 1];
    }
    
}