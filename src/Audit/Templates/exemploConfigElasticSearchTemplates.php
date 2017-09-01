<?php

//exemplo para criar um arquivo de configuracao
return [
    'prefix' => env('AUDIT_PREFIX', ''),
    'index'  => [
        'log' => [
            'index' => $env('AUDIT_PREFIX', '').'log',
            'body'  => [
                'settings' => [
                    'index' => [
                        'refresh_interval' => '5s',
                    ],
                ],
                'mappings' => [
                    'log' => [
                        'properties' => [
                            'timestamp' => [
                                'type'   => 'date',
                                'store'  => 'yes',
                                'format' => 'date_time',
                            ],
                            'route' => [
                                'type'  => 'string',
                                'store' => 'yes',
                                'index' => 'not_analyzed',
                            ],
                            'user' => [
                                'type'  => 'integer',
                                'store' => 'yes',
                                'index' => 'not_analyzed',
                            ],
                            'ipAddress' => [
                                'type'  => 'string',
                                'store' => 'yes',
                                'index' => 'not_analyzed',
                            ],
                            'entity' => [
                                'type'  => 'string',
                                'store' => 'yes',
                                'index' => 'not_analyzed',
                            ],
                            'content' => [
                                'type'  => 'string',
                                'store' => 'yes',
                                'index' => 'not_analyzed',
                            ],
                            'idEntity' => [
                                'type'  => 'string',
                                'store' => 'yes',
                                'index' => 'not_analyzed',
                            ],
                        ],
                    ],
                ],
            ],
        ],
        'changes' => [
            'index' => $env('AUDIT_PREFIX', '').'changes',
            'body'  => [
                'settings' => [
                    'index' => [
                        'refresh_interval' => '5s',
                    ],
                ],
                'mappings' => [
                    'log' => [
                        'properties' => [
                            'timestamp' => [
                                'type'   => 'date',
                                'store'  => 'yes',
                                'format' => 'date_time',
                            ],
                            'route' => [
                                'type'  => 'string',
                                'store' => 'yes',
                                'index' => 'not_analyzed',
                            ],
                            'user' => [
                                'type'  => 'integer',
                                'store' => 'yes',
                                'index' => 'not_analyzed',
                            ],
                            'ipAddress' => [
                                'type'  => 'string',
                                'store' => 'yes',
                                'index' => 'not_analyzed',
                            ],
                            'entity' => [
                                'type'  => 'string',
                                'store' => 'yes',
                                'index' => 'not_analyzed',
                            ],
                            'content' => [
                                'type'  => 'string',
                                'store' => 'yes',
                                'index' => 'not_analyzed',
                            ],
                            'idEntity' => [
                                'type'  => 'string',
                                'store' => 'yes',
                                'index' => 'not_analyzed',
                            ],
                        ],
                    ],
                ],
            ],
        ],
        // ...
    ],
];
