<?php

namespace Bludata\Audit\Libs;

use Bludata\Audit\Contracts\AuditInterface;
use Elasticsearch\ClientBuilder;

class ElasticsearchAudit implements AuditInterface
{
    protected $elasticsearch;

    public function __construct()
    {
        $this->refreshClient();
    }

    public function refreshClient()
    {
        $hosts = [
            [
                'host' => env('AUDIT_HOST', 'localhost'),
                'port' => env('AUDIT_PORT', '9200'),
                'scheme' => env('AUDIT_SCHEME', 'http')
            ],
        ];
        $elasticsearch = ClientBuilder::create();
        $elasticsearch->setHosts($hosts);
        $elasticsearch->allowBadJSONSerialization();
        $this->elasticsearch = $elasticsearch->build();
    }

    /**
     * Gerar em uma migration com os templates criados
     */
    public function createIndex($index = null)
    {
        if (!$this->elasticsearch->indices()->exists(['index' => $index['index']])) {
            return $this->elasticsearch->indices()->create($index);
        }
    }

    /**
     * @inheritdoc
     */
    public function put($index, $value, $template = 'log')
    {
        if (!count($value)) {
            throw new \InvalidArgumentException('Body can\'t be empty');
        }

        $params = [
            'index' => env('AUDIT_INDEX', $index ? $index : ''),
            'body' => $value,
            'type' => $template,
        ];

        $response = $this->elasticsearch->index($params);
        return $response;
    }

    /**
     * @inheritdoc
     */
    public function get($key, $id = null, $type = 'log')
    {
        $params = [
            'index' => $this->prefix . $key,
            'id' => $id,
            'type' => $type,
        ];

        $result = $this->elasticsearch->get($params);

        if (!$result['found']) {
            abort(404, "Indíce não encontrado");
        }

        return $result["_source"];
    }

    /**
     * @inheritdoc
     */
    public function delete($index)
    {
        return $this->elasticsearch->indices()->delete(['index' => $index]);
    }

    public function matchField($field, $value, $index = null)
    {
        $json['query']['match'][$field] = $value;
        $indexParams['body'] = $json;
        if ($index) {
            $indexParams['index'] = $index;
        }
        return $this->elasticsearch->search($indexParams);
    }

    public function filterTerm($field, $value, $index = null)
    {
        $json['query']['constant_score']['filter']['term'][$field] = $value;
        $indexParams['body'] = $json;
        if ($index) {
            $indexParams['index'] = $index;
        }
        return $this->elasticsearch->search($indexParams);
    }

    public function matchPhrase($field, $value, $index = null)
    {
        $json['query']['match_phrase'][$field] = $value;
        $indexParams['body'] = $json;
        if ($index) {
            $indexParams['index'] = $index;
        }
        return $this->elasticsearch->search($indexParams);
    }

    public function disMaxQueries($conditions, $index = null)
    {
        $json['query']['dis_max']['queries'] = $conditions;
        $indexParams['body'] = $json;
        if ($index) {
            $indexParams['index'] = $index;
        }
        return $this->elasticsearch->search($indexParams);
    }

    public function search($condition, $index = null)
    {
        $json['query']['query_string']['query'] = $condition;
        $indexParams['body'] = $json;
        if ($index) {
            $indexParams['index'] = $index;
        }
        return $this->elasticsearch->search($indexParams);
    }
}
