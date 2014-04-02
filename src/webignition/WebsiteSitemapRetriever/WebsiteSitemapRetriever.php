<?php

namespace webignition\WebsiteSitemapRetriever;

use webignition\WebResource\Sitemap\Sitemap;
use Symfony\Component\EventDispatcher\EventDispatcher;  
use webignition\WebsiteSitemapRetriever\Events;

/**
 * Retrieve over HTTP a website's sitemap and make this available as a Sitemap 
 * object
 * 
 */
class WebsiteSitemapRetriever {      
    
    /**
     *
     * @var \Symfony\Component\EventDispatcher\EventDispatcher 
     */
    private $dispatcher = null;    
    
    
    /**
     *
     * @var \webignition\WebsiteSitemapRetriever\Configuration\Configuration
     */
    private $configuration = null;
    
    
    /**
     *
     * @var float
     */
    private $totalTransferTime = 0;    
    
    
    public function __construct() {
        $this->dispatcher = new EventDispatcher();
        $this->dispatcher->addListener(Events::TRANSFER_PRE, array(new Listener\Transfer\PreEventListener(), 'onPreAction'));
        $this->dispatcher->addListener(Events::TRANSFER_POST, array(new Listener\Transfer\PostEventListener(), 'onPostAction'));
        $this->dispatcher->addListener(Events::TRANSFER_TOTAL_TIMEOUT, array(new Listener\Transfer\TotalTimeoutEventListener(), 'onTimeoutAction'));
    }
    
    
    /**
     * 
     * @return \webignition\WebsiteSitemapRetriever\Configuration\Configuration
     */
    public function getConfiguration() {
        if (is_null($this->configuration)) {
            $this->configuration = new Configuration\Configuration();
        }
        
        return $this->configuration;
    }
    
        
    public function reset() {
        $this->totalTransferTime = 0;
    }

    
    
    /**
     * 
     * @return float
     */
    public function getTotalTransferTime() {
        return $this->totalTransferTime;
    }
    
    
    /**
     * 
     * @param float $addition
     */
    public function appendTotalTransferTime($addition) {
        $this->totalTransferTime += $addition;
    }
   

    /**
     * 
     * @param \webignition\WebResource\Sitemap\Sitemap $sitemap
     * @return boolean
     */
    public function retrieve(Sitemap $sitemap) {
        if ($this->getConfiguration()->getShouldHalt()) {
            return false;
        }
        
        $request = clone $this->getConfiguration()->getBaseRequest();
        $request->setUrl($sitemap->getUrl());
        
        $this->setRequestCookies($request);
        $this->setRequestTimeout($request);
        
        $events = $this->getPreAndPostTransferEvents();        
        $this->dispatcher->dispatch(Events::TRANSFER_PRE, $events['pre']);

        $lastRequestException = null;
        
        try {
            $response = $request->send();
        } catch (\Guzzle\Http\Exception\CurlException $curlException) {
            $lastRequestException = $curlException;         
        } catch (\Guzzle\Http\Exception\RequestException $requestException) {                        
            $lastRequestException = $requestException;
        }   
        
        $this->dispatcher->dispatch(Events::TRANSFER_POST, $events['post']);

        if ($lastRequestException instanceof \Exception || $response->getStatusCode() !== 200) {
            return false;
        }        
        
        $sitemap->setHttpResponse($response);

        if ($sitemap->isIndex()) {

            $childUrls = $sitemap->getUrls();

            foreach ($childUrls as $childUrl) {
                $childSitemap = new Sitemap();
                $childSitemap->setConfiguration($sitemap->getConfiguration());
                $childSitemap->setUrl($childUrl);
                $sitemap->addChild($childSitemap);

                if ($this->getConfiguration()->getRetrieveChildSitemaps()) {
                    $this->retrieve($childSitemap);
                }
            }
        }
        
        return true;
    }
    
    
    private function setRequestCookies(\Guzzle\Http\Message\Request $request) {
        if (!is_null($request->getCookies())) {
            foreach ($request->getCookies() as $name => $value) {
                $request->removeCookie($name);
            }
        }        
        
        $cookieUrlMatcher = new \webignition\Cookie\UrlMatcher\UrlMatcher();
        
        foreach ($this->getConfiguration()->getCookies() as $cookie) {
            if ($cookieUrlMatcher->isMatch($cookie, $request->getUrl())) {
                $request->addCookie($cookie['name'], $cookie['value']);
            }
        } 
    }    
    
    
    /**
     * 
     * @param \Guzzle\Http\Message\Request $request
     */
    private function setRequestTimeout(\Guzzle\Http\Message\Request $request) {                        
        $request->getCurlOptions()->set(CURLOPT_TIMEOUT_MS, ($this->getConfiguration()->getTotalTransferTimeout() - $this->getTotalTransferTime()) * 1000);
    }    
    
    
    /**
     * 
     * @return array
     */
    private function getPreAndPostTransferEvents() {
        $preTransferEvent = new Event\Transfer\PreEvent($this);
        $postTransferEvent = new Event\Transfer\PostEvent($this, $preTransferEvent);        
        
        return array(
            'pre' => $preTransferEvent,
            'post' => $postTransferEvent
        );
    }

}