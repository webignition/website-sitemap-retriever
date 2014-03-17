<?php

namespace webignition\Tests\WebsiteSitemapRetriever;

use Guzzle\Http\Client as HttpClient;
use webignition\WebsiteSitemapRetriever\WebsiteSitemapRetriever;
use webignition\WebResource\Sitemap\Sitemap;
use webignition\WebResource\Sitemap\Configuration as SitemapConfiguration;

abstract class BaseTest extends \PHPUnit_Framework_TestCase {    
    
    const FIXTURES_BASE_PATH = '/../../../fixtures';    
    
    /**
     *
     * @var string
     */
    private $fixturePath;
    
        
    /**
     *
     * @var \Guzzle\Http\Client
     */
    private $httpClient = null;    
    
    
    /**
     *
     * @var WebsiteSitemapRetriever
     */
    private $sitemapRetriever = null;
    
    
    public function setUp() {
        $this->setTestFixturePath(get_class($this), $this->getName());
    }
    
    
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
            $this->httpClient->addSubscriber(new \Guzzle\Plugin\History\HistoryPlugin());
        }
        
        return $this->httpClient;
    }
    
    
    /**
     * 
     * @return \Guzzle\Plugin\History\HistoryPlugin|null
     */
    protected function getHttpHistory() {
        $listenerCollections = $this->getHttpClient()->getEventDispatcher()->getListeners('request.sent');
        
        foreach ($listenerCollections as $listener) {
            if ($listener[0] instanceof \Guzzle\Plugin\History\HistoryPlugin) {
                return $listener[0];
            }
        }
        
        return null;     
    }   
    
    /**
     * 
     * @return WebsiteSitemapRetriever
     */
    protected function getSitemapRetriever() {
        if (is_null($this->sitemapRetriever)) {
            $this->sitemapRetriever = new WebsiteSitemapRetriever();
            $this->sitemapRetriever->getConfiguration()->setBaseRequest($this->getHttpClient()->get());
        }
        
        return $this->sitemapRetriever;
    }  

    /**
     * 
     * @param string $testClass
     * @param string $testMethod
     */
    protected function setTestFixturePath($testClass, $testMethod = null) {
        $this->fixturePath = __DIR__ . self::FIXTURES_BASE_PATH . '/' . str_replace('\\', '/', $testClass);       
        
        if (!is_null($testMethod)) {
            $this->fixturePath .= '/' . $testMethod;
        }
    }    
    
    
    /**
     * 
     * @return string
     */
    protected function getTestFixturePath() {
        return $this->fixturePath;     
    }
    
    /**
     * 
     * @param string $fixtureName
     * @return string
     */
    protected function getFixture($fixtureName) {        
        if (file_exists($this->getTestFixturePath() . '/' . $fixtureName)) {
            return file_get_contents($this->getTestFixturePath() . '/' . $fixtureName);
        }
        
        return file_get_contents(__DIR__ . self::FIXTURES_BASE_PATH . '/Common/' . $fixtureName);        
    }
    
    
    protected function setHttpFixtures($fixtures) {        
        $plugin = new \Guzzle\Plugin\Mock\MockPlugin();
        
        foreach ($fixtures as $fixture) {
            if ($fixture instanceof \Exception) {
                $plugin->addException($fixture);
            } else {
                $plugin->addResponse($fixture);
            }
        }
         
        $this->getHttpClient()->addSubscriber($plugin);              
    }
    
    
    /**
     * 
     * @param array $items Collection of http messages and/or curl exceptions
     * @return array
     */
    protected function buildHttpFixtureSet($items) {
        $fixtures = array();
        
        foreach ($items as $item) {
            switch ($this->getHttpFixtureItemType($item)) {
                case 'httpMessage':
                    $fixtures[] = \Guzzle\Http\Message\Response::fromMessage($item);
                    break;
                
                case 'curlException':
                    $fixtures[] = $this->getCurlExceptionFromCurlMessage($item);                    
                    break;
                
                default:
                    throw new \LogicException();
            }
        }
        
        return $fixtures;
    }
    
    protected function getHttpFixtures($path, $filter = null) {
        $items = array();

        $fixturesDirectory = new \DirectoryIterator($path);
        $fixturePaths = array();
        foreach ($fixturesDirectory as $directoryItem) {
            if ($directoryItem->isFile() && ((!is_array($filter)) || (is_array($filter) && in_array($directoryItem->getFilename(), $filter)))) {                
                $fixturePaths[] = $directoryItem->getPathname();
            }
        }
        
        sort($fixturePaths);        
        
        foreach ($fixturePaths as $fixturePath) {
            $items[] = file_get_contents($fixturePath);
        }
        
        return $this->buildHttpFixtureSet($items);
    }
    
    /**
     * 
     * @param string $item
     * @return string
     */
    private function getHttpFixtureItemType($item) {
        if (substr($item, 0, strlen('HTTP')) == 'HTTP') {
            return 'httpMessage';
        }
        
        return 'curlException';
    }    
    
    
    /**
     *
     * @param string $testName
     * @return string
     */
    protected function getFixturesDataPath($testName = null) {
        return (is_null($testName))
            ? $this->fixturePath
            : $this->fixturePath . '/' . $testName;
    } 
    
    
    /**
     * 
     * @param string $curlMessage
     * @return \Guzzle\Http\Exception\CurlException
     */
    private function getCurlExceptionFromCurlMessage($curlMessage) {
        $curlMessageParts = explode(' ', $curlMessage, 2);
        
        $curlException = new \Guzzle\Http\Exception\CurlException();
        $curlException->setError($curlMessageParts[1], (int)  str_replace('CURL/', '', $curlMessageParts[0]));
        
        return $curlException;
    }    
    
    
}