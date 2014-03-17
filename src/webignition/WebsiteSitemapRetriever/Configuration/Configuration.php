<?php

namespace webignition\WebsiteSitemapRetriever\Configuration;

class Configuration {    
    
    /**
     * @var int
     */
    const DEFAULT_TOTAL_TRANSFER_TIMEOUT = 60;
    
    
    /**
     *
     * @var float
     */
    private $totalTransferTimeout = null;    
    

    /**
     *
     * @var boolean
     */
    private $retrieveChildSitemaps = true;
    
    
    /**
     *
     * @var boolean
     */
    private $shouldHalt = false;
    
    
    /**
     *
     * @var \Guzzle\Http\Message\Request
     */
    private $baseRequest = null;
    
    
    /**
     *
     * @var array
     */
    private $cookies = array();    
    

    /**
     * 
     * @param \Guzzle\Http\Message\Request $request
     * @return \webignition\WebsiteSitemapRetriever\Configuration\WebsiteSitemapRetriever
     */
    public function setBaseRequest(\Guzzle\Http\Message\Request $request) {
        $this->baseRequest = $request;
        return $this;
    }
    
    
    
    /**
     * 
     * @return \Guzzle\Http\Message\Request $request
     */
    public function getBaseRequest() {
        if (is_null($this->baseRequest)) {
            $client = new \Guzzle\Http\Client;            
            $this->baseRequest = $client->get();
        }
        
        return $this->baseRequest;
    }
    
    
    /**
     * 
     * @return \webignition\WebsiteSitemapRetriever\Configuration\WebsiteSitemapRetriever
     */
    public function enableShouldHalt() {
        $this->shouldHalt = true;
        return $this;
    }
    
    
    /**
     * 
     * @return \webignition\WebsiteSitemapRetriever\Configuration\WebsiteSitemapRetriever
     */
    public function disableShouldHalt() {
        $this->shouldHalt = false;
        return $this;
    }
    
    
    /**
     * 
     * @return boolean
     */
    public function getShouldHalt() {
        return $this->shouldHalt;
    }
    
    
    
    /**
     * 
     * @param float $timeout
     * @return \webignition\WebsiteSitemapRetriever\Configuration\WebsiteSitemapRetriever
     */
    public function setTotalTransferTimeout($timeout) {
        $this->totalTransferTimeout = $timeout;
        return $this;
    }
    
    
    /**
     * 
     * @return float
     */
    public function getTotalTransferTimeout() {
        return (is_null($this->totalTransferTimeout)) ? self::DEFAULT_TOTAL_TRANSFER_TIMEOUT : $this->totalTransferTimeout;
    }

    
    /**
     * 
     * @return \webignition\WebsiteSitemapRetriever\Configuration\WebsiteSitemapRetriever
     */
    public function enableRetrieveChildSitemaps() {
        $this->retrieveChildSitemaps = true;
        return $this;
    }

    
    /**
     * 
     * @return \webignition\WebsiteSitemapRetriever\Configuration\WebsiteSitemapRetriever
     */
    public function disableRetrieveChildSitemaps() {
        $this->retrieveChildSitemaps = false;
        return $this;
    }
    
    
    /**
     * 
     * @return boolean
     */
    public function getRetrieveChildSitemaps() {
        return $this->retrieveChildSitemaps;
    }
    
    
    /**
     * 
     * @param array $cookies
     * @return \webignition\CssValidatorWrapper\Configuration\Configuration
     */
    public function setCookies($cookies) {
        $this->cookies = $cookies;
        return $this;
    }
    
    
    /**
     * 
     * @return array
     */
    public function getCookies() {
        return $this->cookies;
    }    

}