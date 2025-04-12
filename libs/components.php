<?php

declare(strict_types=1);
trait Components
{
    public static $components = [
        'switch' => [
            'output' => [
                'type'         => VARIABLETYPE_BOOLEAN,
                'name'         => 'State',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_SWITCH,
                ],
                'writable' => true
            ],
            'apower' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Last measured instantaneous active power',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => 'W',
                ],
                'writable' => false
            ],
            'voltage' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Last measured voltage',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => 'V',
                ],
                'writable' => false
            ],
            'current' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Last measured current in Amperes',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => 'A',
                ],
                'writable' => false
            ],
            'pf' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Last measured power factor',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                ],
                'writable' => false
            ],
            'freq' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Last measured network frequency',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => 'Hz'
                ],
                'writable' => false
            ],
            'aenergy' => [
                'total' => [
                    'type'         => VARIABLETYPE_FLOAT,
                    'name'         => 'Total energy consumed in',
                    'factor'       => 0.001,
                    'presentation' => [
                        'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                        'SUFFIX'       => 'kw/h'
                    ],
                    'writable' => false
                ],
            ],
            'ret_aenergy' => [
                'total' => [
                    'type'         => VARIABLETYPE_FLOAT,
                    'name'         => 'Total returned energy',
                    'factor'       => 0.001,
                    'presentation' => [
                        'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                        'SUFFIX'       => 'kw/h'
                    ],
                    'writable' => false
                ],
            ],
            'temperature' => [
                'tC' => [
                    'type'         => VARIABLETYPE_FLOAT,
                    'name'         => 'Temperature',
                    'presentation' => [
                        'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                        'SUFFIX'       => '°C'
                    ],
                    'writable' => false
                ],
            ],
            'errors' => [
                'type'         => VARIABLETYPE_STRING,
                'name'         => 'Errors',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                ],
                'writable' => false
            ],
        ],
    ];
}