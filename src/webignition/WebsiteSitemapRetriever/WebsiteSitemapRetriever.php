<?php
namespace webignition\WebsiteSitemapRetriever;

use webignition\InternetMediaType\InternetMediaType;
use webignition\InternetMediaType\Parser\Parser as InternetMediaTypeParser;
use webignition\WebResource\Sitemap\Sitemap;
use Guzzle\Http\Client as HttpClient;

/**
 * Retrieve over HTTP a website's sitemap and make this available as a Sitemap 
 * object
 * 
 */
class WebsiteSitemapRetriever {
    
    /**
     * Collection of content types for compressed content
     * 
     * @var array
     */
    private $compressedContentTypes = array(
        'application/x-gzip'
    );    
    
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
     * @var boolean
     */
    private $limitRetrievedUrlCount = false;
    
    
    /**
     *
     * @var int
     */
    private $retrievedUrlCountThreshold = null;
   
    
    public function retrieve(Sitemap $sitemap) {        
        $request = $this->getHttpClient()->get($sitemap->getUrl());
        
        try {
            $response = $request->send();            
        } catch (\Guzzle\Http\Exception\RequestException $requestException) {
            return false;
        }
        
        if ($response->getStatusCode() !== 200) {
            return false;
        }
        
        $mediaTypeParser = new InternetMediaTypeParser();
        $contentType = $mediaTypeParser->parse($response->getHeader('content-type'));
        
        $content = $this->extractGzipContent($response->getBody());

        $sitemap->setContentType((string)$contentType);
        $sitemap->setContent($content);
        
        if ($sitemap->isIndex()) {
            if ($this->retrieveChildSitemaps) {                
                $childUrls = $sitemap->getUrls();

                foreach ($childUrls as $childUrl) {                    
                    $childSitemap = new Sitemap();                
                    $childSitemap->setConfiguration($sitemap->getConfiguration());
                    $childSitemap->setUrl($childUrl);                    
                    $sitemap->addChild($childSitemap);
                    
                    if (!$this->hasTotalUrlCountExceededThreshold($sitemap)) {
                        $this->retrieve($childSitemap);
                    }
                }                 
            }           
        }
        
        return true;          
    }
    
    private function hasTotalUrlCountExceededThreshold(Sitemap $sitemap) {
        if ($this->limitRetrievedUrlCount === false) {
            return false;
        }
        
        return $this->getTotalSitemapUrlCount($sitemap) > $this->getRetrievedUrlCountThreshold();        
    }
    
    
    /**
     * 
     * @param \webignition\WebResource\Sitemap\Sitemap $sitemap
     * @return int
     */
    private function getTotalSitemapUrlCount(Sitemap $sitemap) {
        if (!$sitemap->isSitemap()) {
            return 0;
        }
        
        if (!$sitemap->isIndex()) {
            return count($sitemap->getUrls());
        }
        
        $urls = array();
        foreach  ($sitemap->getChildren() as $childSitemap) {
            /* @var $childSitemap Sitemap */
            $urls = array_merge($urls, $childSitemap->getUrls());
        }
        
        return count($urls);
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
        $destinationFilename = $sourceFilename.'.xml';
        
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
    
    
    public function enableLimitRetrievedUrlCount() {
        $this->limitRetrievedUrlCount = true;
    }
    
    public function disableLimitRetrievedUrlCount() {
        $this->limitRetrievedUrlCount = false;
    }  
    
    public function setRetrievedUrlCountThreshold($threshold = null) {
        $this->retrievedUrlCountThreshold = $threshold;
    }
    
    public function getRetrievedUrlCountThreshold() {
        return $this->retrievedUrlCountThreshold;
    }    
    
}