<?php

use Guzzle\Http\Client as HttpClient;
use webignition\WebsiteSitemapRetriever\WebsiteSitemapRetriever;
use webignition\WebResource\Sitemap\Sitemap;
use webignition\WebResource\Sitemap\Configuration as SitemapConfiguration;

abstract class BaseTest extends PHPUnit_Framework_TestCase {
    
        
    /**
     *
     * @var MockHttpClient
     */
    private $httpClient = null;    
    
    
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
            'application/rss+xml' => 'webignition\WebResource\Sitemap\UrlExtractor\NewsFeedUrlExtractor',
            'sitemaps.org.xml.index' => 'webignition\WebResource\Sitemap\UrlExtractor\SitemapsOrgXmlIndexUrlExtractor',
        ));

        $sitemap = new Sitemap();
        $sitemap->setConfiguration($configuration);
        return $sitemap;
    }    
    
    
    /**
     * 
     * @return \Guzzle\Http\Client
     */
    protected function getHttpClient() {
        if (is_null($this->httpClient)) {
            $this->httpClient = new HttpClient();
        }
        
        return $this->httpClient;
    }  
    
    /**
     * 
     * @return WebsiteSitemapRetriever
     */
    protected function getSitemapRetriever() {
        if (is_null($this->sitemapRetriever)) {
            $this->sitemapRetriever = new WebsiteSitemapRetriever();
            $this->sitemapRetriever->setHttpClient($this->getHttpClient());
        }
        
        return $this->sitemapRetriever;
    }
    
    
    
//    /**
//     * 
//     * @param string $testClass
//     * @param string $testMethod
//     * @return string
//     */
//    private function getTestFixturePath($testClass, $testMethod) {
//        return __DIR__ . '/fixtures/' . $testClass . '/' . $testMethod;       
//    }
//    
//    
//    /**
//     * Set the mock HTTP client test fixtures path based on the
//     * test class and test method to be run
//     * 
//     * @param string $testClass
//     * @param string $testMethod
//     */
//    protected function setTestFixturePath($testClass, $testMethod) {
//        $this->getHttpClient()->getStoredResponseList()->setFixturesPath(
//            $this->getTestFixturePath($testClass, $testMethod)
//        );
//    }
    
    
    
    protected function setHttpFixtures($fixtures) {
        $plugin = new \Guzzle\Plugin\Mock\MockPlugin();
        
        foreach ($fixtures as $fixture) {
            $plugin->addResponse($fixture);
        }
         
        $this->getHttpClient()->addSubscriber($plugin);              
    }
    
    
    protected function getHttpFixtures($path) {
        $fixtures = array();
        
        $fixturesDirectory = new \DirectoryIterator($path);
        foreach ($fixturesDirectory as $directoryItem) {
            if ($directoryItem->isFile()) {                
                $fixtures[] = \Guzzle\Http\Message\Response::fromMessage(file_get_contents($directoryItem->getPathname()));
            }
        }
        
        return $fixtures;
    } 
    

    /**
     *
     * @param string $testName
     * @return string
     */
    protected function getFixturesDataPath($className, $testName) {        
        return __DIR__ . '/fixtures/' . str_replace('\\', DIRECTORY_SEPARATOR, $className) . '/' . $testName;
    }     
    
    
//    /**
//     * 
//     * @param \HttpRequest $request
//     */
//    protected function storeHttpResponseAsFixture(\HttpRequest $request, \Closure $callback = null) {        
//        $fixturePath = $this->getHttpClient()->getStoredResponseList()->getRequestFixturePath($request);
//        $fixturePathParts = explode('/', $fixturePath);
//        
//        $currentPath = '';
//        
//        for ($partIndex = 1; $partIndex < count($fixturePathParts) - 1; $partIndex++) {
//            $fixturePathPart = $fixturePathParts[$partIndex];
//            if ($fixturePathPart != '') {
//                $currentPath .= '/' . $fixturePathPart;
//                
//                if (!is_dir($currentPath)) {
//                    mkdir($currentPath);
//                }            
//            }            
//        }
//        
//        $request->send();
//        
//        $rawResponseContent = $request->getRawResponseMessage();
//
//        if (!is_null($callback)) {
//            $rawResponseContent = $callback($rawResponseContent);
//        }
//        
//        
//        file_put_contents(
//            $this->getHttpClient()->getStoredResponseList()->getRequestFixturePath($request),
//            $rawResponseContent
//        );       
//    }
    
    
}