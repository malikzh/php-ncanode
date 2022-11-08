<?php

namespace Feature;

use Malikzh\PhpNCANode\NCANodeClient;
use PHPUnit\Framework\TestCase;

class NCANodeClientTest extends TestCase
{
    public function test_can_create_client()
    {
        $client = new NCANodeClient('http://ncanode:14579/');

        $this->assertTrue($client->nodeInfo()['status'] === 0);
    }
}