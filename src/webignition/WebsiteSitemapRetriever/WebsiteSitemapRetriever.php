<?php

namespace webignition\WebsiteSitemapRetriever;

use webignition\InternetMediaType\Parser\Parser as InternetMediaTypeParser;
use webignition\WebResource\Sitemap\Sitemap;
use Symfony\Component\EventDispatcher\EventDispatcher;  
use webignition\WebsiteSitemapRetriever\Events;

/**
 * Retrieve over HTTP a website's sitemap and make this available as a Sitemap 
 * object
 * 
 */
class WebsiteSitemapRetriever {
    
    const HTTP_AUTH_BASIC_NAME = 'Basic';
    const HTTP_AUTH_DIGEST_NAME = 'Digest';
    
    
    private $httpAuthNameToCurlAuthScheme = array(
        self::HTTP_AUTH_BASIC_NAME => CURLAUTH_BASIC,
        self::HTTP_AUTH_DIGEST_NAME => CURLAUTH_DIGEST
    );
    
    
    /**
     * @var int
     */
    const DEFAULT_TOTAL_TRANSFER_TIMEOUT = 60;

    /**
     *
     * @var \Guzzle\Http\Client
     */
    private $httpClient = null;

    /**
     *
     * @var boolean
     */
    private $retrieveChildSitemaps = true;    
    
    /**
     *
     * @var \Symfony\Component\EventDispatcher\EventDispatcher 
     */
    private $dispatcher = null;    
    
    /**
     *
     * @var float
     */
    private $totalTransferTime = 0;
    
    
    /**
     *
     * @var float
     */
    private $totalTransferTimeout = null;
    
    
    /**
     *
     * @var boolean
     */
    private $shouldHalt = false;
    
    
    /**
     *
     * @var string
     */
    private $httpAuthenticationUser = '';
    
    /**
     *
     * @var string
     */
    private $httpAuthenticationPassword = '';
    
    
    
    public function __construct() {
        $this->dispatcher = new EventDispatcher();
        $this->dispatcher->addListener(Events::TRANSFER_PRE, array(new Listener\Transfer\PreEventListener(), 'onPreAction'));
        $this->dispatcher->addListener(Events::TRANSFER_POST, array(new Listener\Transfer\PostEventListener(), 'onPostAction'));
        $this->dispatcher->addListener(Events::TRANSFER_TOTAL_TIMEOUT, array(new Listener\Transfer\TotalTimeoutEventListener(), 'onTimeoutAction'));
    } 
    
    
    /**
     * 
     * @param string $user
     */
    public function setHttpAuthenticationUser($user) {
        $this->httpAuthenticationUser = $user;
    }
    
    
    /**
     * 
     * @param string $password
     */
    public function setHttpAuthenticationPassword($password) {
        $this->httpAuthenticationPassword = $password;
    }
    
    
    /**
     * 
     * @return string
     */
    public function getHttpAuthenticationUser() {
        return $this->httpAuthenticationUser;
    }
    
    
    /**
     * 
     * @return string
     */
    public function getHttpAuthenticationPassword() {
        return $this->httpAuthenticationPassword;
    }
    
    
    public function enableShouldHalt() {
        $this->shouldHalt = true;
    }
    
        
    public function reset() {
        $this->totalTransferTime = 0;
    }
    
    
    /**
     * 
     * @param float $timeout
     */
    public function setTotalTransferTimeout($timeout) {
        $this->totalTransferTimeout = $timeout;
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
        if ($this->shouldHalt === true) {
            return false;
        }
        
        $request = $this->getHttpClient()->get($sitemap->getUrl());                
        $this->setRequestTimeout($request);
        
        $events = $this->getPreAndPostTransferEvents();        
        $this->dispatcher->dispatch(Events::TRANSFER_PRE, $events['pre']);

        $lastRequestException = null;
        
        try {
            $response = $this->getSitemapResourceResponse($request);            
        } catch (\Guzzle\Http\Exception\CurlException $curlException) {
            $lastRequestException = $curlException;         
        } catch (\Guzzle\Http\Exception\RequestException $requestException) {                        
            $lastRequestException = $requestException;
        }
        
        $this->dispatcher->dispatch(Events::TRANSFER_POST, $events['post']);

        if ($lastRequestException instanceof \Exception || $response->getStatusCode() !== 200) {
            return false;
        }        

        $mediaTypeParser = new InternetMediaTypeParser();
        $contentType = $mediaTypeParser->parse($response->getHeader('content-type'));
        
        $content = $this->extractGzipContent($response->getBody());       
        
        $sitemap->setContentType((string) $contentType);
        $sitemap->setContent($content);

        if ($sitemap->isIndex()) {

            $childUrls = $sitemap->getUrls();

            foreach ($childUrls as $childUrl) {
                $childSitemap = new Sitemap();
                $childSitemap->setConfiguration($sitemap->getConfiguration());
                $childSitemap->setUrl($childUrl);
                $sitemap->addChild($childSitemap);

                if ($this->retrieveChildSitemaps) {
                    $this->retrieve($childSitemap);
                }
            }
        }
        
        return true;
    } 
    
    
    private function getSitemapResourceResponse(\Guzzle\Http\Message\Request $request, $failOnAuthenticationFailure = false) {
        try {
            return $request->send();     
        } catch (\Guzzle\Http\Exception\ClientErrorResponseException $clientErrorResponseException) {
            /* @var $response \Guzzle\Http\Message\Response */
            $response = $clientErrorResponseException->getResponse();            
            $authenticationScheme = $this->getWwwAuthenticateSchemeFromResponse($response);
            
            if (is_null($authenticationScheme) || $failOnAuthenticationFailure) {
                throw $clientErrorResponseException;
            }            

            $request->setAuth($this->getHttpAuthenticationUser(), $this->getHttpAuthenticationPassword(), $this->getWwwAuthenticateSchemeFromResponse($response));
            return $this->getSitemapResourceResponse($request, true);
        }        
    }
    
    
    /**
     * 
     * @param \Guzzle\Http\Message\Response $response
     * @return int|null
     */
    private function getWwwAuthenticateSchemeFromResponse(\Guzzle\Http\Message\Response $response) {
        if ($response->getStatusCode() !== 401) {
            return null;
        }
        
        if (!$response->hasHeader('www-authenticate')) {
            return null;
        }        
              
        $wwwAuthenticateHeaderValues = $response->getHeader('www-authenticate')->toArray();
        $firstLineParts = explode(' ', $wwwAuthenticateHeaderValues[0]);

        return (isset($this->httpAuthNameToCurlAuthScheme[$firstLineParts[0]])) ? $this->httpAuthNameToCurlAuthScheme[$firstLineParts[0]] : null;    
    }
    
    
    /**
     * 
     * @param \Guzzle\Http\Message\Request $request
     */
    private function setRequestTimeout(\Guzzle\Http\Message\Request $request) {                        
        $request->getCurlOptions()->set(CURLOPT_TIMEOUT_MS, ($this->getTotalTransferTimeout() - $this->getTotalTransferTime()) * 1000);
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

    /**
     *
     * @param \Guzzle\Http\Client $client 
     */
    public function setHttpClient(\Guzzle\Http\Client $client) {
        $this->httpClient = $client;
    }

    /**
     *
     * @return \webignition\Http\Client\Client 
     */
    private function getHttpClient() {
        if (is_null($this->httpClient)) {
            $this->httpClient = new \Guzzle\Http\Client;
        }

        return $this->httpClient;
    }

    /**
     * 
     * @param string $gzippedContent
     * @return string
     */
    private function extractGzipContent($gzippedContent) {
        $sourceFilename = sys_get_temp_dir() . '/' . md5(microtime(true));
        $destinationFilename = $sourceFilename . '.xml';

        file_put_contents($sourceFilename, $gzippedContent);

        $sfp = gzopen($sourceFilename, "rb");
        $fp = fopen($destinationFilename, "w");

        while ($string = gzread($sfp, 4096)) {
            fwrite($fp, $string, strlen($string));
        }

        gzclose($sfp);
        fclose($fp);

        return file_get_contents($destinationFilename);
    }

    public function enableRetrieveChildSitemaps() {
        $this->retrieveChildSitemaps = true;
    }

    public function disableRetrieveChildSitemaps() {
        $this->retrieveChildSitemaps = false;
    }

}