<?php

use PHPUnit\Framework\TestCase;
use Gnorm\GnormTwigConfiguration;

class GnormTwigConfigurationTest extends TestCase
{
    public function testDecodeValidJson()
    {
        $json = '{"key": "value", "number": 123}';
        $expected = ['key' => 'value', 'number' => 123];

        $result = GnormTwigConfiguration::decode($json);

        $this->assertIsArray($result);
        $this->assertEquals($expected, $result);
    }

    public function testDecodeInvalidJson()
    {
        $json = '{"key": "value", "number": 123'; // Missing closing brace

        $result = GnormTwigConfiguration::decode($json);

        $this->assertNull($result);
    }

    public function testDecodeEmptyString()
    {
        $json = '';

        $result = GnormTwigConfiguration::decode($json);

        $this->assertNull($result);
    }

    public function testDecodeNonJsonString()
    {
        $json = 'Just a regular string';

        $result = GnormTwigConfiguration::decode($json);

        $this->assertNull($result);
    }
}
