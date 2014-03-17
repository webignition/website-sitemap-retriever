<?php

namespace webignition\Tests\WebsiteSitemapRetriever\Configuration;

class TotalTransferTimeoutTest extends ConfigurationTest {
    
    public function testGetDefaultTotalTransferTimeout() {
        $this->assertEquals(\webignition\WebsiteSitemapRetriever\Configuration\Configuration::DEFAULT_TOTAL_TRANSFER_TIMEOUT, $this->configuration->getTotalTransferTimeout());
    }
    
    
    public function testSetReturnsSelf() {
        $this->assertEquals($this->configuration, $this->configuration->setTotalTransferTimeout(10));
    }
    
    public function testGetValueSet() {
        $timeout = 17;
        $this->assertEquals($timeout, $this->configuration->setTotalTransferTimeout($timeout)->getTotalTransferTimeout());
    }
    
}