<?php

namespace Bludata\Tests\Soap;

use SoapClient as NativeSoapClient;
use Bludata\Tests\TestCase;
use Bludata\Soap\SoapClient;

class SoapClientTest extends TestCase
{
    public function clients()
    {
        $wsdls = [
            'https://portalhomolog.detran.go.gov.br/sah/WSHabilitacaoSimulador?wsdl'
        ];
        $clients = collect();
        foreach ($wsdls as $wsdl) {
            $clients->push(new SoapClient($wsdl));
        }
        return $clients;
    }

    public function testIsInstanciable()
    {
        $this->assertTrue(
            class_exists(SoapClient::class),
            'classe "Bludata\Soap\SoapClient" não encontrada'
        );

        $clients = $this->clients();
        $clients->each(function ($client) {
            $this->assertInstanceOf(SoapClient::class, $client);
        });
        return $clients;
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Host não informado
     */
    public function testCallWithoutHost()
    {
        $clients = $this->clients();
        $clients->each(function ($client) {
            $client->setHost('');
            $client->call();
        });
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Serviço não informado
     */
    public function testCallWithoutService()
    {
        $clients = $this->clients();
        $clients->each(function ($client) {
            $client->setService('');
            $client->call();
        });
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Request não informada
     */
    public function testCallWithoutRequest()
    {
        $clients = $this->clients();
        $clients->each(function ($client) {
            $client->setService('retornarAulasCandidato');
            $client->setRequest([]);
            $client->call();
        });
    }

    public function testCall()
    {
        $clients = $this->clients();
        $clients->each(function ($client) {
            $client->setService('retornarAulasCandidato');
            $client->setRequest(['aluno' => 'anyone']);
            $response = $client->call();
            $this->assertObjectHasAttribute('return', $response);
        });
    }

    public function testConnect()
    {
        $clients = $this->clients();
        $clients->each(function ($client) {
            $this->assertInstanceOf(SoapClient::class, $client->connect());
        });
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Host não informado
     */
    public function testConnectWithoutPassingHost()
    {
        $clients = $this->clients();
        $clients->each(function ($client) {
            $client->setHost('');
            $client->connect();
        });
    }

    public function testSetHost()
    {
        $clients = $this->clients();
        $clients->each(function ($client) {
            $this->assertTrue(method_exists($client, 'setHost'), 'método "setHost" não encontrado');
            $client->setHost('http://example.com');
            $this->assertObjectHasAttribute('host', $client);
            $this->assertAttributeEquals('http://example.com', 'host', $client);
        });
    }

    /**
     * @depends testSetHost
     */
    public function testGetHost()
    {
        $clients = $this->clients();
        $clients->each(function ($client) {
            $this->assertTrue(method_exists($client, 'getHost'), 'método "getHost" não encontrado');
            $host = $client->getHost();
            $this->assertNotEmpty($host);
        });
    }

    public function testSetOptions()
    {
        $clients = $this->clients();
        $clients->each(function ($client) {
            $this->assertTrue(method_exists($client, 'setOptions'), 'método "setOptions" não encontrado');
            $client->setOptions(['this' => 'is a test']);
            $this->assertObjectHasAttribute('options', $client);
            $this->assertAttributeEquals(['this' => 'is a test'], 'options', $client);
        });
    }

    /**
     * @depends testSetOptions
     */
    public function testGetOptions()
    {
        $clients = $this->clients();
        $clients->each(function ($client) {
            $this->assertTrue(method_exists($client, 'getOptions'), 'método "getOptions" não encontrado');
            $client->setOptions(['this' => 'is another test']);
            $options = $client->getOptions();
            $this->assertNotEmpty($options);
        });
    }

    public function testSetRequest()
    {
        $clients = $this->clients();
        $clients->each(function ($client) {
            $this->assertTrue(method_exists($client, 'setRequest'), 'método "setRequest" não encontrado');
            $client->setRequest(['this' => 'is a request test']);
            $this->assertObjectHasAttribute('request', $client);
            $this->assertAttributeEquals(['this' => 'is a request test'], 'request', $client);
        });
    }

    /**
     * @depends testSetRequest
     */
    public function testGetRequest()
    {
        $clients = $this->clients();
        $clients->each(function ($client) {
            $this->assertTrue(method_exists($client, 'getRequest'), 'método "getRequest" não encontrado');
            $client->setRequest(['this' => 'is another request test']);
            $request = $client->getRequest();
            $this->assertNotEmpty($request);
        });
    }

    public function testSetService()
    {
        $clients = $this->clients();
        $clients->each(function ($client) {
            $this->assertTrue(method_exists($client, 'setService'), 'método "setService" não encontrado');
            $client->setService('someService');
            $this->assertObjectHasAttribute('service', $client);
            $this->assertAttributeEquals('someService', 'service', $client);
        });
    }

    /**
     * @depends testSetService
     */
    public function testGetService()
    {
        $clients = $this->clients();
        $clients->each(function ($client) {
            $this->assertTrue(method_exists($client, 'getService'), 'método "getService" não encontrado');
            $client->setService('someService');
            $service = $client->getService();
            $this->assertNotEmpty($service);
        });
    }

    public function testSetClient()
    {
        $clients = $this->clients();
        $clients->each(function ($client) {
            $this->assertTrue(method_exists($client, 'setClient'), 'método "setClient" não encontrado');
            $nativeCliente = new NativeSoapClient($client->getHost());
            $client->setClient($nativeCliente);
            $this->assertObjectHasAttribute('client', $client);
            $this->assertAttributeEquals($nativeCliente, 'client', $client);
        });
    }

    /**
     * @depends testSetClient
     */
    public function testGetClient()
    {
        $clients = $this->clients();
        $clients->each(function ($client) {
            $this->assertTrue(method_exists($client, 'getClient'), 'método "getClient" não encontrado');
            $nativeCliente = new NativeSoapClient($client->getHost());
            $client->setClient($nativeCliente);
            $nativeClient = $client->getClient();
            $this->assertNotEmpty($nativeClient);
        });
    }
}
