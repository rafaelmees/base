<?php

namespace Bludata\Soap;

use InvalidArgumentException;
use SoapClient as NativeSoapClient;
use SoapFault;

class SoapClient
{
    /**
     * @var string
     */
    protected $host;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var SoapClient
     */
    protected $client;

    /**
     * @var mixed
     */
    protected $service;

    /**
     * @var mixed
     */
    protected $request;

    /**
     * Cria um instancia para comunicação com WSDL utilizando SoapClient.
     *
     * @param string $host    Parametro obrigatório, endereço do WSDL
     * @param array  $options Parametro opcional, referente aos options que serão utilizados
     */
    public function __construct($host, array $options = [])
    {
        $this->setHost($host)
            ->setOptions($options);
    }

    /**
     * Efetua a conexão inicial com o WSDL.
     *
     * @return self
     */
    public function connect()
    {
        if (!$host = $this->getHost()) {
            throw new InvalidArgumentException('Host não informado');
        }

        if (!$this->client) {
            $this->client = new NativeSoapClient($host, $this->getOptions());
        }

        return $this;
    }

    /**
     * Faz a chamada para o WSDL, de acordo com os parametros pré-configurados para o serviço.
     *
     * @return SoapClient::__soapCall
     */
    public function call()
    {
        if (!$this->getHost()) {
            throw new InvalidArgumentException('Host não informado');
        }

        if (!$this->getService()) {
            throw new InvalidArgumentException('Serviço não informado');
        }

        if (!$this->getRequest()) {
            throw new InvalidArgumentException('Request não informada');
        }

        $this->connect();
        $client = $this->getClient();
        return $client->__soapCall($this->getService(), $this->getRequest());
    }

    /**
     * Retorna o host do WSDL.
     *
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Recebe o endereço do WSDL.
     *
     * @param string $host
     *
     * @return self
     */
    public function setHost($host)
    {
        $this->host = $host;

        return $this;
    }

    /**
     * Retorna os options utilizados no SoapClient.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Recebe os options que serão utilizados no SoapClient.
     *
     * @param array $options
     *
     * @return self
     */
    public function setOptions(array $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Retorna dados da requisição.
     *
     * @return mixed
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Recebe dados para montar o request.
     *
     * @param array
     *
     * @return self
     */
    public function setRequest($params)
    {
        if (!$request = $this->getRequest()) {
            $request = [];
        }

        foreach ($params as $param => $value) {
            $request[$param] = $value;
        }

        $this->request = $request;

        return $this;
    }

    /**
     * Retorna o serviço utilizado.
     *
     * @return mixed
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Recebe o serviço que sera utilizado no WSDL.
     *
     * @param string $service
     *
     * @return self
     */
    public function setService($service)
    {
        $this->service = $service;

        return $this;
    }

    /**
     * Retorna a instancia do SoapClient que foi incializada.
     *
     * @return mixed
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Recebe o SoapClient que sera utilizado.
     *
     * @param SoapClient $client
     *
     * @return self
     */
    public function setClient(NativeSoapClient $client)
    {
        $this->client = $client;

        return $this;
    }
}
