<?php

use webignition\Http\Mock\Client\Client as MockHttpClient;
use webignition\WebsiteSitemapRetriever\WebsiteSitemapRetriever;
use webignition\WebResource\Sitemap\Sitemap;
use webignition\WebResource\Sitemap\Configuration as SitemapConfiguration;

abstract class BaseTest extends PHPUnit_Framework_TestCase {
    
        
    /**
     *
     * @var MockHttpClient
     */
    private $mockHttpClient = null;    
    
    
    /**
     *
     * @var WebsiteSitemapRetriever
     */
    private $sitemapRetriever = null;
    
    
    /**
     * 
     * @return \webignition\WebResource\Sitemap\Sitemap
     */
    protected function createSitemap() {
        $configuration = new SitemapConfiguration;
        $configuration->setTypeToUrlExtractorClassMap(array(
            'sitemaps.org.xml' => 'webignition\WebResource\Sitemap\UrlExtractor\SitemapsOrgXmlUrlExtractor',
            'sitemaps.org.txt' => 'webignition\WebResource\Sitemap\UrlExtractor\SitemapsOrgTxtUrlExtractor',
            'application/atom+xml' => 'webignition\WebResource\Sitemap\UrlExtractor\NewsFeedUrlExtractor',
            'application/rss+xml' => 'webignition\WebResource\Sitemap\UrlExtractor\NewsFeedUrlExtractor'
        ));

        $sitemap = new Sitemap();
        $sitemap->setConfiguration($configuration);
        return $sitemap;
    }    
    
    
    /**
     * 
     * @return MockHttpClient
     */
    protected function getMockHttpClient() {
        if (is_null($this->mockHttpClient)) {
            $this->mockHttpClient = new MockHttpClient();
            $this->mockHttpClient->getStoredResponseList()->setFixturesPath(__DIR__ . '/fixtures');
        }
        
        return $this->mockHttpClient;
    }  
    
    /**
     * 
     * @return WebsiteSitemapRetriever
     */
    protected function getSitemapRetriever() {
        if (is_null($this->sitemapRetriever)) {
            $this->sitemapRetriever = new WebsiteSitemapRetriever();
            $this->sitemapRetriever->setHttpClient($this->getMockHttpClient());
        }
        
        return $this->sitemapRetriever;
    }
    
    
    
    /**
     * 
     * @param string $testClass
     * @param string $testMethod
     * @return string
     */
    private function getTestFixturePath($testClass, $testMethod) {
        return __DIR__ . '/fixtures/' . $testClass . '/' . $testMethod;       
    }
    
    
    /**
     * Set the mock HTTP client test fixtures path based on the
     * test class and test method to be run
     * 
     * @param string $testClass
     * @param string $testMethod
     */
    protected function setTestFixturePath($testClass, $testMethod) {
        $this->getMockHttpClient()->getStoredResponseList()->setFixturesPath(
            $this->getTestFixturePath($testClass, $testMethod)
        );
    }
    
    
    /**
     * 
     * @param \HttpRequest $request
     */
    protected function storeHttpResponseAsFixture(\HttpRequest $request, \Closure $callback = null) {        
        $fixturePath = $this->getMockHttpClient()->getStoredResponseList()->getRequestFixturePath($request);
        $fixturePathParts = explode('/', $fixturePath);
        
        $currentPath = '';
        
        for ($partIndex = 1; $partIndex < count($fixturePathParts) - 1; $partIndex++) {
            $fixturePathPart = $fixturePathParts[$partIndex];
            if ($fixturePathPart != '') {
                $currentPath .= '/' . $fixturePathPart;
                
                if (!is_dir($currentPath)) {
                    mkdir($currentPath);
                }            
            }            
        }
        
        $request->send();
        
        $rawResponseContent = $request->getRawResponseMessage();

        if (!is_null($callback)) {
            $rawResponseContent = $callback($rawResponseContent);
        }
        
        
        file_put_contents(
            $this->getMockHttpClient()->getStoredResponseList()->getRequestFixturePath($request),
            $rawResponseContent
        );       
    }
    
    
}