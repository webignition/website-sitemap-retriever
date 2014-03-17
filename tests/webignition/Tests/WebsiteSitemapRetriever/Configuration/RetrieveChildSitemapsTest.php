<?php

namespace webignition\Tests\WebsiteSitemapRetriever\Configuration;

class RetrieveChildSitemapsTest extends ConfigurationTest {
    
    public function testDefaultIsTrue() {
        $this->assertTrue($this->configuration->getRetrieveChildSitemaps());
    }    
    
    public function testEnableReturnsSelf() {
        $this->assertEquals($this->configuration, $this->configuration->enableRetrieveChildSitemaps());
    }
    
    public function testDisableReturnsSelf() {
        $this->assertEquals($this->configuration, $this->configuration->disableRetrieveChildSitemaps());
    }    
    
    public function testEnable() {
        $this->assertTrue($this->configuration->enableRetrieveChildSitemaps()->getRetrieveChildSitemaps());
    }

    public function testDisable() {
        $this->assertFalse($this->configuration->disableRetrieveChildSitemaps()->getRetrieveChildSitemaps());
    }    
}