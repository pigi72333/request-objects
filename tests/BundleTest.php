<?php

use \Fesor\RequestObject\Examples;
use \Fesor\RequestObject\Examples\App;
use \Symfony\Component\HttpFoundation\Request;

class BundleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var App\AppKernel
     */
    private $kernel;

    function setUp()
    {
        $kernel = new App\AppKernel('test', true);
        $kernel->boot();

        $this->kernel = $kernel;
    }
    
    function testRequest()
    {
        $payload = [
            'email' => 'user@example.com',
            'password' => 'example',
            'first_name' => 'John',
            'last_name' => 'Doe'
        ];
        $response = $this->kernel->handle(Request::create('/users', 'POST', $payload));

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals($payload, json_decode($response->getContent(), true));
    }

    /**
     * @expectedException \Fesor\RequestObject\InvalidRequestPayloadException
     */
    function testInvalidRequestData()
    {
        $payload = [
            'email' => 'invalid',
            'password' => 'example',
            'first_name' => 'John',
            'last_name' => 'Doe'
        ];
        
        $this->kernel->handle(Request::create('/users', 'POST', $payload));
    }

    function testCustomInvalidRequestResponse()
    {
        $payload = [
            'email' => 'invalid',
            'password' => 'example',
            'first_name' => 'John',
            'last_name' => 'Doe'
        ];

        $response = $this->kernel->handle(Request::create('/users_custom', 'POST', $payload));
        $responseBody = json_decode($response->getContent(), true);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertCount(1, $responseBody['errors']);
    }

    function testNoCustomRequest()
    {
        $response = $this->kernel->handle(Request::create('/no_request', 'POST', []));
        $this->assertEquals(204, $response->getStatusCode());
    }
}
