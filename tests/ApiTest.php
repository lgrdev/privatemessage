<?php
declare(strict_types=1);

namespace LgrDev\Tests;

use PHPUnit\Framework\TestCase;

class ApiTest  extends TestCase
{
    private $http;

    public function testAuthentification()
    {
        try {
        $this->http = new \GuzzleHttp\Client(['base_uri' => 'https://test.lgrdev.ovh']);


        $response = $this->http->get( '/api/v1/message/1', [
            'headers' => [
                'Authorization' => 'test@test.com:000000000',
                'User-Agent' => "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88 Safari/537.36",
            ],

        ]);
    } catch (\GuzzleHttp\Exception\ClientException $e) {

        var_dump($e->getResponse()->getBody()->getContents());

    }
        var_dump($response);
        $this->assertEquals(401, $response->getStatusCode());

        $response = $this->http->request('DELETE', '/api/v1/message/1', [
            'headers' => [
                'Authorization' => 'test@test.com:000000000'
            ]
        ]);
        $this->assertEquals(401, $response->getStatusCode());
      
        $response = $this->http->request('POST', '/api/v1/message', [
            'headers' => [
                'Authorization' => 'test@test.com:000000000'
            ]
        ]);
        $this->assertEquals(401, $response->getStatusCode());

        $response = $this->http->request('GET', '/api/v1/message/1', [
            'headers' => [
                'Authorization' => 'test@test.com:000000000'
            ]
        ]);
        $this->assertEquals(401, $response->getStatusCode());

        $response = $this->http->request('DELETE', '/api/v1/message/1');
        $this->assertEquals(401, $response->getStatusCode());
      
        $response = $this->http->request('POST', '/api/v1/message');
        $this->assertEquals(401, $response->getStatusCode());

        $response = $this->http->request('GET', '/api/v1/message/1');
        $this->assertEquals(401, $response->getStatusCode());

    }

    public function testBadRequest()
    {
        $this->http = new \GuzzleHttp\Client(['base_uri' => 'https://test.lgrdev.ovh']);
        $response = $this->http->request('POST', '/api/v1/message', [
            'headers' => [
                'Authorization' => 'test@test.com:40dc6d75ac72021004f68f185c67eb6427ae0274f924e6ba67e3838282600e43'
            ]
        ]);
        $this->assertEquals(400, $response->getStatusCode());


        $response = $this->http->request('GET', '/api/v1/message/1', [
            'headers' => [
                'Authorization' => 'test@test.com:40dc6d75ac72021004f68f185c67eb6427ae0274f924e6ba67e3838282600e43'
            ]
        ]);
        $this->assertEquals(400, $response->getStatusCode());
        
        $response = $this->http->request('DELETE', '/api/v1/message/1', [
            'headers' => [
                'Authorization' => 'test@test.com:40dc6d75ac72021004f68f185c67eb6427ae0274f924e6ba67e3838282600e43'
            ]
        ]);
        $this->assertEquals(400, $response->getStatusCode());

    }

    public function testPostMessageRequest()
    {
        $this->http = new \GuzzleHttp\Client(['base_uri' => 'https://test.lgrdev.ovh']);
        $response = $this->http->request('POST', '/api/v1/message', [
            'headers' => [
                'Authorization' => 'test@test.com:40dc6d75ac72021004f68f185c67eb6427ae0274f924e6ba67e3838282600e43'
            ],
            'Body' => [
                'message' => 'C\'est un test pour voir',
                'expirein' => '1'
            ]
        ]);
        $this->assertEquals(200, $response->getStatusCode());

        $response = json_decode($response->getBody()->getContents(), true);
        $this->assertArrayHasKey('Key', $response);

    }

    public function testGetMessageRequest()
    {
        $this->http = new \GuzzleHttp\Client(['base_uri' => 'https://test.lgrdev.ovh']);
        $response = $this->http->request('POST', '/api/v1/message', [
            'headers' => [
                'Authorization' => 'test@test.com:40dc6d75ac72021004f68f185c67eb6427ae0274f924e6ba67e3838282600e43'
            ],
            'Body' => [
                'message' => 'C\'est un test pour voir',
                'expirein' => '1'
            ]
        ]);
        $this->assertEquals(200, $response->getStatusCode());
        
        $response = json_decode($response->getBody()->getContents(), true);
        $this->assertArrayHasKey('Key', $response);

        $response = $this->http->request('GET', '/api/v1/message/'.$response['Key'], [
            'headers' => [
                'Authorization' => 'test@test.com:40dc6d75ac72021004f68f185c67eb6427ae0274f924e6ba67e3838282600e43'
            ]
        ]);
        $this->assertEquals(200, $response->getStatusCode());

        $response = json_decode($response->getBody()->getContents(), true);
        $this->assertArrayHasKey('Message', $response);

    }

}