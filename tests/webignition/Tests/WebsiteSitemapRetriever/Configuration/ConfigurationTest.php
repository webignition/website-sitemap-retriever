<?php

namespace webignition\Tests\WebsiteSitemapRetriever\Configuration;

use webignition\WebsiteSitemapRetriever\Configuration\Configuration;
use webignition\Tests\WebsiteSitemapRetriever\BaseTest;

abstract class ConfigurationTest extends BaseTest {
    
    /**
     *
     * @var Configuration
     */
    protected $configuration;
    
    public function setUp() { 
        parent::setUp();
        $this->configuration = new Configuration();
    }   
    
}