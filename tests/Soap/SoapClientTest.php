<?php

namespace Bludata\Tests\Soap;

use SoapClient as NativeSoapClient;
use Bludata\Tests\TestCase;
use Bludata\Soap\SoapClient;

class SoapClientTest extends TestCase
{
    public function clients()
    {
        // WS aberto do Detran de Goiás
        $wsDetranGO = new SoapClient('https://portalhomolog.detran.go.gov.br/sah/WSHabilitacaoSimulador?wsdl');
        $wsDetranGO->setService('retornarAulasCandidato');
        $wsDetranGO->setRequest(['aluno' => 'anyone']);

        return [
            [$wsDetranGO]
        ];
    }

    /**
     * @dataProvider clients
     */
    public function testIsInstanciable($client)
    {
        $this->assertTrue(
            class_exists(SoapClient::class),
            'classe "Bludata\Soap\SoapClient" não encontrada'
        );

        $this->assertInstanceOf(SoapClient::class, $client);
        return $client;
    }

    /**
     * @dataProvider clients
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Host não informado
     */
    public function testCallWithoutHost($client)
    {
        $client->setHost('');
        $client->call();
    }

    /**
     * @dataProvider clients
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Serviço não informado
     */
    public function testCallWithoutService($client)
    {
        $client->setService('');
        $client->call();
    }

    /**
     * @dataProvider clients
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Request não informada
     */
    public function testCallWithoutRequest($client)
    {
        $client->setService('retornarAulasCandidato');
        $client->setRequest([]);
        $client->call();
    }

    /**
     * @dataProvider clients
     */
    public function testCall($client)
    {
        $response = $client->call();
        $this->assertObjectHasAttribute('return', $response);
    }

    /**
     * @dataProvider clients
     */
    public function testConnect($client)
    {
        $this->assertInstanceOf(SoapClient::class, $client->connect());
    }

    /**
     * @dataProvider clients
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Host não informado
     */
    public function testConnectWithoutPassingHost($client)
    {
        $client->setHost('');
        $client->connect();
    }

    /**
     * @dataProvider clients
     */
    public function testSetHost($client)
    {
        $this->assertTrue(method_exists($client, 'setHost'), 'método "setHost" não encontrado');
        $client->setHost('http://example.com');
        $this->assertObjectHasAttribute('host', $client);
        $this->assertAttributeEquals('http://example.com', 'host', $client);
    }

    /**
     * @dataProvider clients
     * @depends testSetHost
     */
    public function testGetHost($client)
    {
        $this->assertTrue(method_exists($client, 'getHost'), 'método "getHost" não encontrado');
        $host = $client->getHost();
        $this->assertNotEmpty($host);
    }

    /**
     * @dataProvider clients
     */
    public function testSetOptions($client)
    {
        $this->assertTrue(method_exists($client, 'setOptions'), 'método "setOptions" não encontrado');
        $client->setOptions(['this' => 'is a test']);
        $this->assertObjectHasAttribute('options', $client);
        $this->assertAttributeEquals(['this' => 'is a test'], 'options', $client);
    }

    /**
     * @dataProvider clients
     * @depends testSetOptions
     */
    public function testGetOptions($client)
    {
        $this->assertTrue(method_exists($client, 'getOptions'), 'método "getOptions" não encontrado');
        $client->setOptions(['this' => 'is another test']);
        $options = $client->getOptions();
        $this->assertNotEmpty($options);
    }

    /**
     * @dataProvider clients
     */
    public function testSetRequest($client)
    {
        $this->assertTrue(method_exists($client, 'setRequest'), 'método "setRequest" não encontrado');
        $client->setRequest(['this' => 'is a request test']);
        $this->assertObjectHasAttribute('request', $client);
        $this->assertAttributeEquals(['this' => 'is a request test'], 'request', $client);
    }

    /**
     * @dataProvider clients
     * @depends testSetRequest
     */
    public function testGetRequest($client)
    {
        $this->assertTrue(method_exists($client, 'getRequest'), 'método "getRequest" não encontrado');
        $client->setRequest(['this' => 'is another request test']);
        $request = $client->getRequest();
        $this->assertNotEmpty($request);
    }

    /**
     * @dataProvider clients
     */
    public function testSetService($client)
    {
        $this->assertTrue(method_exists($client, 'setService'), 'método "setService" não encontrado');
        $client->setService('someService');
        $this->assertObjectHasAttribute('service', $client);
        $this->assertAttributeEquals('someService', 'service', $client);
    }

    /**
     * @dataProvider clients
     * @depends testSetService
     */
    public function testGetService($client)
    {
        $this->assertTrue(method_exists($client, 'getService'), 'método "getService" não encontrado');
        $client->setService('someService');
        $service = $client->getService();
        $this->assertNotEmpty($service);
    }

    /**
     * @dataProvider clients
     */
    public function testSetClient($client)
    {
        $this->assertTrue(method_exists($client, 'setClient'), 'método "setClient" não encontrado');
        $nativeCliente = new NativeSoapClient($client->getHost());
        $client->setClient($nativeCliente);
        $this->assertObjectHasAttribute('client', $client);
        $this->assertAttributeEquals($nativeCliente, 'client', $client);
    }

    /**
     * @dataProvider clients
     * @depends testSetClient
     */
    public function testGetClient($client)
    {
        $this->assertTrue(method_exists($client, 'getClient'), 'método "getClient" não encontrado');
        $nativeCliente = new NativeSoapClient($client->getHost());
        $client->setClient($nativeCliente);
        $nativeClient = $client->getClient();
        $this->assertNotEmpty($nativeClient);
    }
}
