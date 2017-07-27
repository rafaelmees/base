<?php

namespace Audit\Libs;
use Audit\Contracts\AuditInterface;
use Elasticsearch\ClientBuilder;

class ElasticsearchAudit implements AuditInterface
{
    protected $elasticsearch;

    public function __construct()
    {
        $this->refreshClient();
        $this->prefix = env('AUDIT_PREFIX');
    }

    public function refreshClient()
    {
        $hosts = [
            [
                'host' => env('AUDIT_HOST'),
                'port' => env('AUDIT_PORT'),
                'scheme' => env('AUDIT_SCHEME'),
                'user' => env('AUDIT_USER'),
                'pass' => env('AUDIT_PASS'),
            ],
        ];
        $elasticsearch = ClientBuilder::create();
        $elasticsearch->setHosts($hosts);
        $this->elasticsearch = $elasticsearch->build();
    }

    /**
     * Gerar em uma migration com os templates criados
     */
    public function createLogIndex($index)
    {
        // be sure that the index doesn't already exist
        if (!$this->elasticsearch->indices()->exists(['index' => $index])) {
            // create index
            $params = $this->generateLogTemplate($index);
            return $this->elasticsearch->indices()->create($params);
        }
    }

    /**
     * @inheritdoc
     */
    public function put($key, $value, $template = 'log')
    {
        if (!count($value)) {
            throw new \InvalidArgumentException('Body can\'t be empty');
        }

        $params = [
            'index' => $this->prefix . $key,
            'body' => $value,
            'id' => $value['idEntity'],
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