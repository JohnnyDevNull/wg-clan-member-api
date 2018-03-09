<?php

namespace Tests\Functional;

class RestApiTest extends BaseTestCase
{
    /**
     * Test that the index route returns a rendered response containing the text 'SlimFramework' but not a greeting
     */
    public function testGetIndexOnline()
    {
        $response = $this->runApp('GET', '/');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('Online', (string)$response->getBody());
        $this->assertNotContains('Page Not Found', (string)$response->getBody());
    }

    /**
     * Test that the index route won't accept a post request
     */
    public function testPostIndexNotAllowed()
    {
        $response = $this->runApp('POST', '/', ['test']);

        $this->assertEquals(405, $response->getStatusCode());
        $this->assertContains('Method not allowed', (string)$response->getBody());
    }

    /**
     * Test that the index route with optional name argument returns a rendered greeting
     */
    public function testGetClansList()
    {
        $response = $this->runApp('GET', '/clans');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('500137827', (string)$response->getBody());
        $this->assertContains('SPVO', (string)$response->getBody());
        $this->assertContains('Spitze Voraus', (string)$response->getBody());
        $this->assertContains('500146159', (string)$response->getBody());
        $this->assertContains('SPVOF', (string)$response->getBody());
        $this->assertContains('Spitze Voraus Fun', (string)$response->getBody());
    }

    /**
     * Test that the index route with optional name argument returns a rendered greeting
     */
    public function testGetSpecifigClan()
    {
        $response = $this->runApp('GET', '/clans/500137827');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('500137827', (string)$response->getBody());
        $this->assertContains('SPVO', (string)$response->getBody());
        $this->assertContains('Spitze Voraus', (string)$response->getBody());
        $this->assertNotContains('500146159', (string)$response->getBody());
        $this->assertNotContains('SPVOF', (string)$response->getBody());
        $this->assertNotContains('Spitze Voraus Fun', (string)$response->getBody());
    }
}