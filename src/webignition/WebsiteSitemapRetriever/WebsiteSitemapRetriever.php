<?php
namespace webignition\WebsiteSitemapRetriever;

use webignition\InternetMediaType\InternetMediaType;
use webignition\InternetMediaType\Parser\Parser as InternetMediaTypeParser;
use webignition\WebResource\Sitemap\Sitemap;

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
     * @var \webignition\Http\Client\Client
     */
    private $httpClient = null;
    
    /**
     *
     * @var boolean
     */
    private $retrieveChildSitemaps = true;
   
    
    public function retrieve(Sitemap $sitemap) {        
        $request = new \HttpRequest($sitemap->getUrl());
        $request->setOptions(array(
            'timeout' => 30
        ));
        
        try {
            $response = $this->getHttpClient()->getResponse($request);                     
        } catch (\webignition\Http\Client\Exception $httpClientException) {
            return false;
        } catch (\webignition\Http\Client\CurlException $curlException) {
            return false;
        }        
        
        if ($response->getResponseCode() != 200) {
            return false;
        }        
        
        $mediaTypeParser = new InternetMediaTypeParser();
        $contentType = $mediaTypeParser->parse($response->getHeader('content-type'));
        
        $content = ($this->isCompressedContentType($contentType)) ? $this->extractGzipContent($response->getBody()) : $response->getBody();

        $sitemap->setContentType((string)$contentType);
        $sitemap->setContent($content);
        
        if ($sitemap->isIndex()) {
            if ($this->retrieveChildSitemaps) {
                $childUrls = $sitemap->getUrls();

                foreach ($childUrls as $childUrl) {
                    $childSitemap = new Sitemap();                
                    $childSitemap->setConfiguration($sitemap->getConfiguration());
                    $childSitemap->setUrl($childUrl);
                    $this->retrieve($childSitemap);
                    $sitemap->addChild($childSitemap);
                }                 
            }           
        }
        
        return true;          
    }
    
    
    /**
     *
     * @param \webignition\Http\Client\Client $client 
     */
    public function setHttpClient(\webignition\Http\Client\Client $client) {
        $this->httpClient = $client;
    }
    
    
    /**
     *
     * @return \webignition\Http\Client\Client 
     */
    private function getHttpClient() {
        if (is_null($this->httpClient)) {
            $this->httpClient = new \webignition\Http\Client\Client();
            $this->httpClient->redirectHandler()->enable();
        }
        
        return $this->httpClient;
    }   
    
    
    /**
     * 
     * @param \webignition\InternetMediaType\InternetMediaType $contentType
     * @return boolean
     */
    private function isCompressedContentType(InternetMediaType $contentType) {
        return in_array($contentType->getTypeSubtypeString(), $this->compressedContentTypes);
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
    
}