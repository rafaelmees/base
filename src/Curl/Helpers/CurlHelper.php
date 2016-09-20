<?php

namespace Bludata\Curl\Helpers;

class CurlHelper
{
    protected $init;
    protected $headers = [];
    protected $response;
    protected $info;
    protected $baseUrl;
    protected $posFixUrl;
    protected $options = [];

    public function __construct($baseUrl, array $headers = [])
    {
        $this->init = curl_init();

        $this->baseUrl = $baseUrl;

        $this->headers = $headers;
    }

    protected function exec()
    {
        curl_setopt($this->init, CURLOPT_URL, trim($this->baseUrl.$this->posFixUrl));
        curl_setopt($this->init, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->init, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($this->init, CURLOPT_SSL_VERIFYPEER, false);

        foreach ($this->options as $key => $value) {
            curl_setopt($this->init, $key, $value);
        }

        $this->response = curl_exec($this->init);

        $this->info = curl_getinfo($this->init);
    }

    public function send($close = true)
    {
        $this->exec();

        if ($close === true) {
            curl_close($this->init);
        }

        return $this;
    }

    public function addHeader($header)
    {
        $this->headers[] = $header;

        return $this;
    }

    public function getResponse()
    {
        return [
            'code' => $this->info['http_code'],
            'data' => $this->response,
        ];
    }

    public function getInfo()
    {
        return $this->info;
    }

    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;

        return $this;
    }

    public function setPosFixUrl($posFixUrl)
    {
        $this->posFixUrl = $posFixUrl;

        return $this;
    }

    public function post(array $data)
    {
        $this->options[CURLOPT_POST] = true;
        $this->options[CURLOPT_POSTFIELDS] = json_encode($data);

        return $this;
    }

    public function put(array $data)
    {
        $this->options[CURLOPT_CUSTOMREQUEST] = 'PUT';
        $this->options[CURLOPT_POSTFIELDS] = json_encode($data);

        return $this;
    }

    public function delete()
    {
        $this->options[CURLOPT_CUSTOMREQUEST] = 'DELETE';

        return $this;
    }
}
