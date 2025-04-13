<?php

declare(strict_types=1);
trait Components
{
    public static $components = [
        'input' => [
            'state' => [
                'type'         => VARIABLETYPE_BOOLEAN,
                'name'         => 'Input State',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_SWITCH,
                ],
                'writable' => false
            ],
            'percent' => [
                'type'         => VARIABLETYPE_INTEGER,
                'name'         => 'Input Percent',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => '%',
                ],
                'writable' => false
            ],

        ],
        'em' => [
            'a_current' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Phase A current measurement value',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' A',
                ],
                'writable' => false
            ],
            'a_voltage' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Phase A voltage measurement value',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' V',
                ],
                'writable' => false
            ],
            'a_act_power' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Phase A active power measurement value',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' W',
                ],
                'writable' => false
            ],
            'a_aprt_power' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Phase A apparent power measurement value,',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' VA',
                ],
                'writable' => false
            ],
            'a_pf' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Phase A power factor measurement value',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                ],
                'writable' => false
            ],
            'a_freq' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Phase A network frequency measurement value',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                ],
                'writable' => false
            ],
            'b_current' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Phase B current measurement value',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' A',
                ],
                'writable' => false
            ],
            'b_voltage' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Phase B voltage measurement value',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' V',
                ],
                'writable' => false
            ],
            'b_act_power' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Phase B active power measurement value',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' W',
                ],
                'writable' => false
            ],
            'b_aprt_power' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Phase B apparent power measurement value,',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' VA',
                ],
                'writable' => false
            ],
            'b_pf' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Phase B power factor measurement value',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                ],
                'writable' => false
            ],
            'b_freq' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Phase B network frequency measurement value',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                ],
                'writable' => false
            ],
            'c_current' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Phase C current measurement value',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' A',
                ],
                'writable' => false
            ],
            'c_voltage' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Phase C voltage measurement value',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' V',
                ],
                'writable' => false
            ],
            'c_act_power' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Phase C active power measurement value',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' W',
                ],
                'writable' => false
            ],
            'c_aprt_power' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Phase C apparent power measurement value,',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' VA',
                ],
                'writable' => false
            ],
            'c_pf' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Phase C power factor measurement value',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                ],
                'writable' => false
            ],
            'c_freq' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Phase C network frequency measurement value',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                ],
                'writable' => false
            ],
            'n_current' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Neutral current measurement value',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' A',
                ],
            ],
            'total_current' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Sum of the current on all phases',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' A',
                ],
                'writable' => false
            ],
            'total_act_power' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Sum of the active power on all phases',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' W',
                ],
                'writable' => false
            ],
            'total_aprt_power' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Sum of the apparent power on all phases',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' VA',
                ],
                'writable' => false
            ],
        ],
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