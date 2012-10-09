<?php

class LiveTestsTest extends PHPUnit_Framework_TestCase {

    public function testLiveSites() {                       
        $siteRoots = array(
            //'http://www.terrysdiary.com/',
            //'http://www.newscientist.com/',
            'http://webignition.net/'
        );
        
        foreach ($siteRoots as $siteRoot) {            
            $finder = new webignition\WebsiteSitemapFinder\WebsiteSitemapFinder();        
            $finder->setRootUrl($siteRoot);
            $sitemaps = $finder->getSitemaps();
            
            var_dump($sitemaps[0]->getUrl());
        }
        
    }    
    
}