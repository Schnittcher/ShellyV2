<?php

declare(strict_types=1);
trait Components
{
    public static $components = [
        //Immer die Event Komponenten hinzufügen wird (wird in der ShellyModule Base createariableListForForm gemacht)
        'events' => [
            'component' => [
                'type'         => VARIABLETYPE_STRING,
                'name'         => 'Event Component',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                ],
            ],
            'event' => [
                'type'         => VARIABLETYPE_STRING,
                'name'         => 'Event',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                ],
            ],
        ],
        'cloud' => [
            'connected' => [
                'type'         => VARIABLETYPE_BOOLEAN,
                'name'         => 'Cloud State',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_SWITCH,
                ],
            ],
        ],
        'eth' => [
            'ip' => [
                'type'         => VARIABLETYPE_STRING,
                'name'         => 'Ethernet IP-Address',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                ],
            ],
        ],
        'wifi' => [
            'sta_ip' => [
                'type'         => VARIABLETYPE_STRING,
                'name'         => 'Wifi IP-Address',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                ],
            ],
            'ssid' => [
                'type'         => VARIABLETYPE_STRING,
                'name'         => 'Wifi SSID',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                ],
            ],
        ],
        /**         'modbus' => [
         * 'enabled' => [
         * 'type'         => VARIABLETYPE_BOOLEAN,
         * 'name'         => 'Modbus State',
         * 'presentation' => [
         * 'PRESENTATION' => VARIABLE_PRESENTATION_SWITCH,
         * ],
         * ],
         * ], */
        'input' => [
            'state' => [
                'type'         => VARIABLETYPE_BOOLEAN,
                'name'         => 'Input State',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_SWITCH,
                ],
            ],
            'percent' => [
                'type'         => VARIABLETYPE_INTEGER,
                'name'         => 'Input (Percent)',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' %',
                ],
            ],
            'counts' => [
                'total' => [
                    'type'         => VARIABLETYPE_INTEGER,
                    'name'         => 'Total Counts',
                    'presentation' => [
                        'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    ],
                ],
            ],
            'freq' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Frequency',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' Hz'
                ],
            ],
        ],
        'voltmeter' => [
            'voltage' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Voltage',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' V',
                ],
            ],
            'xvoltage' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Xvoltage',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' V',
                ],
            ],
        ],
        'flood' => [
            'alarm' => [
                'type'         => VARIABLETYPE_BOOLEAN,
                'name'         => 'Alarm',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'ICON'         => 'Alert',
                    'OPTIONS'      => '[
                        {
                            "Value": true,
                            "Caption": "Alarm",
                            "IconActive": false,
                            "Icon": "",
                            "ColorActive": true,
                            "ColorValue": 65280
                        },
                        {
                            "Value": false,
                            "Caption": "No alarm",
                            "IconActive": false,
                            "Icon": "",
                            "ColorActive": true,
                            "ColorValue": 16711680
                        }
                    ]',
                ],
            ],
            'mute' => [
                'type'         => VARIABLETYPE_BOOLEAN,
                'name'         => 'Mute',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_SWITCH,
                ],
                'action'        => [
                    'method' => 'Flood.Mute',
                    'params' => ['id' => ''
                    ]
                ],
            ],
            'errors' => [
                'type'          => VARIABLETYPE_STRING,
                'componentType' => 'Array',
                'name'          => 'Errors',
                'presentation'  => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                ],
            ],
        ],
        'emdata' => [
            'a_total_act_energy' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Phase A total active energy',
                'factor'       => 0.001,
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' kWh',
                ],
                'writable' => false
            ],
            'a_total_act_ret_energy' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Phase A total active returned energy',
                'factor'       => 0.001,
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' kWh',
                ],
                'writable' => false
            ],
            'b_total_act_energy' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Phase B total active energy',
                'factor'       => 0.001,
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' kWh',
                ],
                'writable' => false
            ],
            'b_total_act_ret_energy' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Phase B total active returned energy',
                'factor'       => 0.001,
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' kWh',
                ],
                'writable' => false
            ],
            'c_total_act_energy' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Phase C total active energy',
                'factor'       => 0.001,
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' kWh',
                ],
                'writable' => false
            ],
            'c_total_act_ret_energy' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Phase C total active returned energy',
                'factor'       => 0.001,
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' kWh',
                ],
                'writable' => false
            ],
            'total_act' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Total active energy',
                'factor'       => 0.001,
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' kWh',
                ],
                'writable' => false
            ],
            'total_act_ret' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Total active returned energy',
                'factor'       => 0.001,
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' kWh',
                ],
                'writable' => false
            ],
        ],
        'em1data' => [
            'total_act_energy' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Total active energy',
                'factor'       => 0.001,
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' kWh',
                ],
                'writable' => false
            ],
            'total_act_ret_energy' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Total active returned energy',
                'factor'       => 0.001,
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' kWh',
                ],
                'writable' => false
            ],
        ],
        'em' => [
            'a_current' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Phase A current',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' A',
                ],
                'writable' => false
            ],
            'a_voltage' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Phase A voltage',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' V',
                ],
                'writable' => false
            ],
            'a_act_power' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Phase A active power',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' W',
                ],
                'writable' => false
            ],
            'a_aprt_power' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Phase A apparent power',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' VA',
                ],
                'writable' => false
            ],
            'a_pf' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Phase A power factor',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                ],
                'writable' => false
            ],
            'a_freq' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Phase A network frequency',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                ],
                'writable' => false
            ],
            'b_current' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Phase B current',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' A',
                ],
                'writable' => false
            ],
            'b_voltage' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Phase B voltage',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' V',
                ],
                'writable' => false
            ],
            'b_act_power' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Phase B active power',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' W',
                ],
                'writable' => false
            ],
            'b_aprt_power' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Phase B apparent power',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' VA',
                ],
                'writable' => false
            ],
            'b_pf' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Phase B power factor',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                ],
                'writable' => false
            ],
            'b_freq' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Phase B network frequency',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                ],
                'writable' => false
            ],
            'c_current' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Phase C current',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' A',
                ],
                'writable' => false
            ],
            'c_voltage' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Phase C voltage',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' V',
                ],
                'writable' => false
            ],
            'c_act_power' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Phase C active power',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' W',
                ],
                'writable' => false
            ],
            'c_aprt_power' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Phase C apparent power',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' VA',
                ],
                'writable' => false
            ],
            'c_pf' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Phase C power factor',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                ],
                'writable' => false
            ],
            'c_freq' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Phase C network frequency',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                ],
                'writable' => false
            ],
            'n_current' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Neutral current',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' A',
                ],
            ],
            'total_current' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Current on all phases',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' A',
                ],
                'writable' => false
            ],
            'total_act_power' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Active power on all phases',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' W',
                ],
                'writable' => false
            ],
            'total_aprt_power' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Aapparent power on all phases',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' VA',
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
                    'SUFFIX'       => ' °C'
                ],
            ],
        ],
        'humidity' => [
            'rh' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Humidity',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' %',
                    'ICON'         => 'Gauge',
                    'MIN'          => 0,
                    'MAX'          => 100,
                    'DIGITS'       => 2,
                ],
            ],
        ],
        'light' => [
            'output' => [
                'type'         => VARIABLETYPE_BOOLEAN,
                'name'         => 'State',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_SWITCH,
                ],
                'action'        => [
                    'method' => 'Light.Set',
                    'params' => ['id' => '', 'on' => ''
                    ]
                ],
            ],
            'brightness' => [
                'type'         => VARIABLETYPE_INTEGER,
                'name'         => 'Brightness',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_SLIDER,
                    'SUFFIX'       => ' %',
                    'USAGE_TYPE'   => 2
                ],
                'action'        => [
                    'method' => 'Light.Set',
                    'params' => ['id' => '', 'brightness' => ''
                    ]
                ],
                'actionWithExtraVariable' => [
                    'type'         => VARIABLETYPE_STRING,
                    'name'         => 'Brightness Action',
                    'presentation' => [
                        'PRESENTATION' => VARIABLE_PRESENTATION_ENUMERATION,
                        'ICON'         => 'Light',
                        'LAYOUT'       => 1,
                        'OPTIONS'      => '[
                            {
                                "Value": "DimUp",
                                "Caption": "Dim up",
                                "IconActive": false,
                                "Icon": "",
                                "ColorValue": 65280
                            },
                            {
                                "Value": "DimDown",
                                "Caption": "Dim down",
                                "IconActive": false,
                                "Icon": "",
                                "ColorValue": 16753920
                            },
                            {
                                "Value": "DimStop",
                                "Caption": "Dim stop",
                                "IconActive": false,
                                "Icon": "",
                                "ColorValue": 16711680
                            }
                        ]',
                    ],
                    'action'        => [
                        'list'   => true,
                        'method' => 'Light.',
                        'params' => ['id' => ''
                        ]
                    ],
                ],
            ],
            'apower' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Active power',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' W',
                ],
            ],
            'voltage' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Voltage',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' V',
                ],
            ],
            'current' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Current',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' A',
                ],
            ],
            'aenergy' => [
                'total' => [
                    'type'         => VARIABLETYPE_FLOAT,
                    'name'         => 'Total energy',
                    'factor'       => 0.001,
                    'presentation' => [
                        'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                        'SUFFIX'       => ' kwh'
                    ],
                ],
            ],
        ],
        'rgbw' => [
            'output' => [
                'type'         => VARIABLETYPE_BOOLEAN,
                'name'         => 'RGBW State',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_SWITCH,
                ],
                'action'        => [
                    'method' => 'RGBW.Set',
                    'params' => ['id' => '', 'on' => ''
                    ]
                ],
            ],
            'rgb' => [
                'type'         => VARIABLETYPE_STRING,
                'name'         => 'RGB',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_COLOR,
                    'ENCODING'     => 0 //RGB
                ],
                'action'        => [
                    'method' => 'RGBW.Set',
                    'params' => ['id' => '', 'rgb' => ''
                    ]
                ],
            ],
            'brightness' => [
                'type'         => VARIABLETYPE_INTEGER,
                'name'         => 'RGBW Brightness',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_SLIDER,
                    'SUFFIX'       => ' %',
                    'USAGE_TYPE'   => 2
                ],
                'action'        => [
                    'method' => 'RGBW.Set',
                    'params' => ['id' => '', 'brightness' => ''
                    ]
                ],
                'actionWithExtraVariable' => [
                    'type'         => VARIABLETYPE_STRING,
                    'name'         => 'RGBW Brightness Action',
                    'presentation' => [
                        'PRESENTATION' => VARIABLE_PRESENTATION_ENUMERATION,
                        'ICON'         => 'Light',
                        'LAYOUT'       => 1,
                        'OPTIONS'      => '[
                            {
                                "Value": "DimUp",
                                "Caption": "Dim up",
                                "IconActive": false,
                                "Icon": "",
                                "ColorValue": 65280
                            },
                            {
                                "Value": "DimDown",
                                "Caption": "Dim down",
                                "IconActive": false,
                                "Icon": "",
                                "ColorValue": 16753920
                            },
                            {
                                "Value": "DimStop",
                                "Caption": "Dim stop",
                                "IconActive": false,
                                "Icon": "",
                                "ColorValue": 16711680
                            }
                        ]',
                    ],
                    'action'        => [
                        'list'   => true,
                        'method' => 'RGBW.',
                        'params' => ['id' => ''
                        ]
                    ],
                ],
            ],
            'white' => [
                'type'         => VARIABLETYPE_INTEGER,
                'name'         => 'RGBW White',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_SLIDER,
                    'PERCENTAGE'   => true,
                    'SUFFIX'       => ' %',
                    'USAGE_TYPE'   => 2,
                    'MIN'          => 0,
                    'MAX'          => 255
                ],
                'action'        => [
                    'method' => 'RGBW.Set',
                    'params' => ['id' => '', 'white' => ''
                    ]
                ],
            ],
            'apower' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'RGBW active power',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' W',
                ],
            ],
            'voltage' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'RGBW voltage',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' V',
                ],
            ],
            'current' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'RGBW current',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' A',
                ],
            ],
            'aenergy' => [
                'total' => [
                    'type'         => VARIABLETYPE_FLOAT,
                    'name'         => 'RGBW Total energy',
                    'factor'       => 0.001,
                    'presentation' => [
                        'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                        'SUFFIX'       => ' kWh'
                    ],
                ],
            ],
            'temperature' => [
                'tC' => [
                    'type'         => VARIABLETYPE_FLOAT,
                    'name'         => 'Temperature',
                    'presentation' => [
                        'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                        'SUFFIX'       => ' °C'
                    ],
                ],
            ],
        ],
        'rgb' => [
            'output' => [
                'type'         => VARIABLETYPE_BOOLEAN,
                'name'         => 'RGB State',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_SWITCH,
                ],
                'action'        => [
                    'method' => 'RGB.Set',
                    'params' => ['id' => '', 'on' => ''
                    ]
                ],
            ],
            'rgb' => [
                'type'         => VARIABLETYPE_STRING,
                'name'         => 'RGB',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_COLOR,
                    'ENCODING'     => 0 //RGB
                ],
                'action'        => [
                    'method' => 'RGB.Set',
                    'params' => ['id' => '', 'rgb' => ''
                    ]
                ],
            ],
            'brightness' => [
                'type'         => VARIABLETYPE_INTEGER,
                'name'         => 'RGB Brightness',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_SLIDER,
                    'SUFFIX'       => ' %',
                    'USAGE_TYPE'   => 2
                ],
                'action'        => [
                    'method' => 'RGB.Set',
                    'params' => ['id' => '', 'brightness' => ''
                    ]
                ],
                'actionWithExtraVariable' => [
                    'type'         => VARIABLETYPE_STRING,
                    'name'         => 'RGB Brightness Action',
                    'presentation' => [
                        'PRESENTATION' => VARIABLE_PRESENTATION_ENUMERATION,
                        'ICON'         => 'Light',
                        'LAYOUT'       => 1,
                        'OPTIONS'      => '[
                            {
                                "Value": "DimUp",
                                "Caption": "Dim up",
                                "IconActive": false,
                                "Icon": "",
                                "ColorValue": 65280
                            },
                            {
                                "Value": "DimDown",
                                "Caption": "Dim down",
                                "IconActive": false,
                                "Icon": "",
                                "ColorValue": 16753920
                            },
                            {
                                "Value": "DimStop",
                                "Caption": "Dim stop",
                                "IconActive": false,
                                "Icon": "",
                                "ColorValue": 16711680
                            }
                        ]',
                    ],
                    'action'        => [
                        'list'   => true,
                        'method' => 'RGB.',
                        'params' => ['id' => ''
                        ]
                    ],
                ],
            ],
            'temperature' => [
                'tC' => [
                    'type'         => VARIABLETYPE_FLOAT,
                    'name'         => 'RGB Temperature',
                    'presentation' => [
                        'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                        'SUFFIX'       => ' °C'
                    ],
                ],
            ],
            'apower' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'RGB active power',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' W',
                ],
            ],
            'voltage' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'RGB voltage',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' V',
                ],
            ],
            'current' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'RGB current',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' A',
                ],
            ],
            'aenergy' => [
                'total' => [
                    'type'         => VARIABLETYPE_FLOAT,
                    'name'         => 'RGB Total energy',
                    'factor'       => 0.001,
                    'presentation' => [
                        'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                        'SUFFIX'       => ' kWh'
                    ],
                ],
            ],
        ],
        'cct' => [
            'output' => [
                'type'         => VARIABLETYPE_BOOLEAN,
                'name'         => 'CCT State',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_SWITCH,
                ],
                'action'        => [
                    'method' => 'CCT.Set',
                    'params' => ['id' => '', 'on' => ''
                    ]
                ],
            ],
            'ct' => [
                'type'         => VARIABLETYPE_INTEGER,
                'name'         => 'CCT color temperature',
                'presentation' => [
                    'PRESENTATION'  => VARIABLE_PRESENTATION_SLIDER,
                    'GRADIENT_TYPE' => 2,
                    'USAGE_TYPE'    => 1,
                    'PERCENTAGE'    => false
                ],
                'action'        => [
                    'method' => 'CCT.Set',
                    'params' => ['id' => '', 'ct' => ''
                    ]
                ],
            ],
            'brightness' => [
                'type'         => VARIABLETYPE_INTEGER,
                'name'         => 'CCT Brightness',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_SLIDER,
                    'SUFFIX'       => ' %',
                    'USAGE_TYPE'   => 2
                ],
                'action'        => [
                    'method' => 'CCT.Set',
                    'params' => ['id' => '', 'brightness' => ''
                    ]
                ],
                'actionWithExtraVariable' => [
                    'type'         => VARIABLETYPE_STRING,
                    'name'         => 'CCT Brightness Action',
                    'presentation' => [
                        'PRESENTATION' => VARIABLE_PRESENTATION_ENUMERATION,
                        'ICON'         => 'Light',
                        'LAYOUT'       => 1,
                        'OPTIONS'      => '[
                            {
                                "Value": "DimUp",
                                "Caption": "Dim up",
                                "IconActive": false,
                                "Icon": "",
                                "ColorValue": 65280
                            },
                            {
                                "Value": "DimDown",
                                "Caption": "Dim down",
                                "IconActive": false,
                                "Icon": "",
                                "ColorValue": 16753920
                            },
                            {
                                "Value": "DimStop",
                                "Caption": "Dim stop",
                                "IconActive": false,
                                "Icon": "",
                                "ColorValue": 16711680
                            }
                        ]',
                    ],
                    'action'        => [
                        'list'   => true,
                        'method' => 'CCT.',
                        'params' => ['id' => ''
                        ]
                    ],
                ],
            ],
            'apower' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'CCT active power',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' W',
                ],
            ],
            'voltage' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'CCT voltage',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' V',
                ],
            ],
            'current' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'CCT current',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' A',
                ],
            ],
            'aenergy' => [
                'total' => [
                    'type'         => VARIABLETYPE_FLOAT,
                    'name'         => 'CCT Total energy',
                    'factor'       => 0.001,
                    'presentation' => [
                        'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                        'SUFFIX'       => ' kWh'
                    ],
                ],
            ],
            'temperature' => [
                'tC' => [
                    'type'         => VARIABLETYPE_FLOAT,
                    'name'         => 'CCT Temperature',
                    'presentation' => [
                        'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                        'SUFFIX'       => ' °C'
                    ],
                ],
            ],
        ],
        'blutrv' => [
            'current_C' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Current Temperature',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' °C'
                ],
                'action'        => [
                    'method' => 'BluTrv.Call',
                    'params' => ['id' => '', 'method' => 'TRV.SetExternalTemperature', 'params' => [
                        'id' => 0, 't_C' => '']
                    ]
                ],
            ],
            'target_C' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Target Temperature',
                'presentation' => [
                    'PRESENTATION'  => VARIABLE_PRESENTATION_SLIDER,
                    'SUFFIX'        => ' °C',
                    'GRADIENT_TYPE' => 1,
                    'STEP_SIZE'     => 0.1,
                    'USAGE_TYPE'    => 0,
                    'MIN'           => 5,
                    'MAX'           => 30,
                    'DIGITS'        => 2
                ],
                'action'        => [
                    'method' => 'BluTrv.Call',
                    'params' => ['id' => '', 'method' => 'TRV.SetTarget', 'params' => [
                        'id' => 0, 'target_C' => '']
                    ]
                ],
            ],
            'pos' => [
                'type'         => VARIABLETYPE_INTEGER,
                'name'         => 'Position',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_SLIDER,
                    'SUFFIX'       => ' %',
                    'USAGE_TYPE'   => 5
                ],
                'action'        => [
                    'method' => 'BluTrv.Call',
                    'params' => ['id' => '', 'method' => 'Trv.SetPosition', 'params' => [
                        'id' => 0, 'pos' => '']
                    ]
                ],
            ],
            'rssi' => [
                'type'         => VARIABLETYPE_INTEGER,
                'name'         => 'RSSI',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                ],
            ],
            'battery' => [
                'type'         => VARIABLETYPE_INTEGER,
                'name'         => 'Battery',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_SLIDER,
                    'SUFFIX'       => ' %'
                ],
            ],
        ],
        'switch' => [
            'output' => [
                'type'         => VARIABLETYPE_BOOLEAN,
                'name'         => 'State',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_SWITCH,
                ],
                'action'        => [
                    'method' => 'Switch.Set',
                    'params' => ['id' => '', 'on' => ''
                    ]
                ],
            ],
            'apower' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Active power',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' W',
                ],
            ],
            'voltage' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Voltage',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' V',
                ],
            ],
            'current' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Current',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' A',
                ],
            ],
            'pf' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Power factor',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                ],
            ],
            'freq' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Network frequency',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' Hz'
                ],
            ],
            'aenergy' => [
                'total' => [
                    'type'         => VARIABLETYPE_FLOAT,
                    'name'         => 'Total energy',
                    'factor'       => 0.001,
                    'presentation' => [
                        'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                        'SUFFIX'       => ' kWh'
                    ],
                ],
            ],
            'ret_aenergy' => [
                'total' => [
                    'type'         => VARIABLETYPE_FLOAT,
                    'name'         => 'Total returned energy',
                    'factor'       => 0.001,
                    'presentation' => [
                        'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                        'SUFFIX'       => ' kWh'
                    ],
                ],
            ],
            'temperature' => [
                'tC' => [
                    'type'         => VARIABLETYPE_FLOAT,
                    'name'         => 'Temperature',
                    'presentation' => [
                        'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                        'SUFFIX'       => ' °C'
                    ],
                ],
            ],
            'errors' => [
                'type'         => VARIABLETYPE_STRING,
                'name'         => 'Errors',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                ],
            ],
        ],
        'pm1' => [
            'voltage' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Voltage',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' V',
                ],
            ],
            'current' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Current',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' A',
                ],
            ],
            'apower' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Active power',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' W',
                ],
            ],
            'aprtpower' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Apparent power',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' VA',
                ],
                'writable' => false
            ],
            'pf' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Power factor',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                ],
            ],
            'freq' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Network frequency',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' Hz'
                ],
            ],
            'aenergy' => [
                'total' => [
                    'type'         => VARIABLETYPE_FLOAT,
                    'name'         => 'Total energy',
                    'factor'       => 0.001,
                    'presentation' => [
                        'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                        'SUFFIX'       => ' kWh'
                    ],
                ],
            ],
            'ret_aenergy' => [
                'total' => [
                    'type'         => VARIABLETYPE_FLOAT,
                    'name'         => 'Total returned energy',
                    'factor'       => 0.001,
                    'presentation' => [
                        'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                        'SUFFIX'       => ' kWh'
                    ],
                ],
            ],
            'errors' => [
                'type'         => VARIABLETYPE_STRING,
                'name'         => 'Errors',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                ],
            ],
        ],
        'em1' => [
            'current' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Current',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' A',
                ],
            ],
            'voltage' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Voltage',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' V',
                ],
            ],
            'act_power' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Active power',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' W',
                ],
            ],
            'aprt_power' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Apparent power',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' VA',
                ],
                'writable' => false
            ],
            'pf' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Power factor',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                ],
            ],
            'freq' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Network frequency',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' Hz'
                ],
            ],
            'errors' => [
                'type'         => VARIABLETYPE_STRING,
                'name'         => 'Errors',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                ],
            ],
        ],
        'cover' => [
            'state' => [
                'type'         => VARIABLETYPE_STRING,
                'name'         => 'State',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_ENUMERATION,
                    'ICON'         => 'Shutter',
                    'LAYOUT'       => 1,
                    'OPTIONS'      => '[
                        {
                            "Value": "opening",
                            "Caption": "Opening",
                            "IconActive": false,
                            "Icon": "",
                            "ColorValue": 65280
                        },
                        {
                            "Value": "stopped",
                            "Caption": "Stopped",
                            "IconActive": false,
                            "Icon": "",
                            "ColorValue": 16753920
                        },
                        {
                            "Value": "closing",
                            "Caption": "Closing",
                            "IconActive": false,
                            "Icon": "",
                            "ColorValue": 16711680
                        }
                    ]',
                ],
                'actionWithExtraVariable' => [
                    'type'         => VARIABLETYPE_STRING,
                    'name'         => 'Action State',
                    'presentation' => [
                        'PRESENTATION' => VARIABLE_PRESENTATION_ENUMERATION,
                        'ICON'         => 'Shutter',
                        'LAYOUT'       => 1,
                        'OPTIONS'      => '[
                            {
                                "Value": "open",
                                "Caption": "Open",
                                "IconActive": false,
                                "Icon": "",
                                "ColorValue": 65280
                            },
                            {
                                "Value": "stop",
                                "Caption": "Stop",
                                "IconActive": false,
                                "Icon": "",
                                "ColorValue": 16753920
                            },
                            {
                                "Value": "close",
                                "Caption": "Close",
                                "IconActive": false,
                                "Icon": "",
                                "ColorValue": 16711680
                            }
                        ]',
                    ],
                    'action'        => [
                        'list'   => true,
                        'method' => 'Cover.',
                        'params' => ['id' => ''
                        ]
                    ],
                ],
            ],
            'current_pos' => [
                'type'         => VARIABLETYPE_INTEGER,
                'name'         => 'Current Position',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_SLIDER,
                    'ICON'         => 'Shutter',
                    'SUFFIX'       => ' %',
                ],
                'actionWithExtraVariable' => [
                    'type'         => VARIABLETYPE_INTEGER,
                    'name'         => 'Position State',
                    'presentation' => [
                        'PRESENTATION' => VARIABLE_PRESENTATION_SHUTTER,
                        'ICON'         => 'Shutter',
                    ],
                    'action'        => [
                        'method' => 'Cover.GoToPosition',
                        'params' => ['id' => '', 'pos' => ''
                        ]
                    ],
                ],
            ],
            'apower' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Active power',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' W',
                ],
            ],
            'voltage' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Voltage',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' V',
                ],
            ],
            'current' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Current',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' A',
                ],
            ],
            'pf' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Power factor',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                ],
            ],
            'freq' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Network frequency',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' Hz'
                ],
            ],
            'aenergy' => [
                'total' => [
                    'type'         => VARIABLETYPE_FLOAT,
                    'name'         => 'Total energy',
                    'factor'       => 0.001,
                    'presentation' => [
                        'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                        'SUFFIX'       => ' kWh'
                    ],
                ],
            ],
            'temperature' => [
                'tC' => [
                    'type'         => VARIABLETYPE_FLOAT,
                    'name'         => 'Temperature',
                    'presentation' => [
                        'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                        'SUFFIX'       => ' °C'
                    ],
                ],
            ],
        ],
        'smoke' => [
            'alarm' => [
                'type'         => VARIABLETYPE_BOOLEAN,
                'name'         => 'Alarm',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'ICON'         => 'Alert',
                    'OPTIONS'      => '[
                        {
                            "Value": true,
                            "Caption": "Smoke",
                            "IconActive": false,
                            "Icon": "",
                            "ColorActive": true,
                            "ColorValue": 65280
                        },
                        {
                            "Value": false,
                            "Caption": "No smoke",
                            "IconActive": false,
                            "Icon": "",
                            "ColorActive": true,
                            "ColorValue": 16711680
                        }
                    ]',
                ],
            ],
            'mute' => [
                'type'         => VARIABLETYPE_BOOLEAN,
                'name'         => 'Mute',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_SWITCH,
                ],
                'action'        => [
                    'method' => 'Smoke.Mute',
                    'params' => ['id' => ''
                    ]
                ],
            ],
        ],
        'devicepower' => [
            'battery' => [
                'V' => [
                    'type'         => VARIABLETYPE_FLOAT,
                    'name'         => 'Battery voltage',
                    'presentation' => [
                        'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                        'SUFFIX'       => ' V',
                    ],
                ],
                'percent' => [
                    'type'         => VARIABLETYPE_INTEGER,
                    'name'         => 'Battery status',
                    'presentation' => [
                        'PRESENTATION' => VARIABLE_PRESENTATION_SLIDER,
                        'SUFFIX'       => ' %'
                    ],
                ],
            ],
            'external' => [
                'present' => [
                    'type'         => VARIABLETYPE_BOOLEAN,
                    'name'         => 'External power source',
                    'presentation' => [
                        'PRESENTATION' => VARIABLE_PRESENTATION_SWITCH,
                    ],
                ],
            ],
        ],
        // Shelly Presence G4 (https://shelly-api-docs.shelly.cloud/gen2/Devices/Gen4/ShellyPresenceG4).
        // Die eigentliche Anwesenheitserkennung steckt in den einzelnen "presencezone:X"-Instanzen
        // (bis zu 10), nicht in der übergeordneten "presence"-Komponente selbst - die liefert im
        // normalen Status kaum eigene Werte (nur "live_track", nur während eines aktiven
        // Presence.LiveTrack-Aufrufs), deshalb wird "presence" hier nicht extra abgebildet.
        'illuminance' => [
            'lux' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Illuminance',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' lx',
                ],
            ],
            'illumination' => [
                'type'         => VARIABLETYPE_STRING,
                'name'         => 'Illumination',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_ENUMERATION,
                    'LAYOUT'       => 1,
                    'OPTIONS'      => '[
                        {
                            "Value": "dark",
                            "Caption": "Dark",
                            "IconActive": false,
                            "Icon": "",
                            "ColorValue": 16711680
                        },
                        {
                            "Value": "twilight",
                            "Caption": "Twilight",
                            "IconActive": false,
                            "Icon": "",
                            "ColorValue": 16753920
                        },
                        {
                            "Value": "bright",
                            "Caption": "Bright",
                            "IconActive": false,
                            "Icon": "",
                            "ColorValue": 65280
                        }
                    ]',
                ],
            ],
            'errors' => [
                'type'          => VARIABLETYPE_STRING,
                'componentType' => 'Array',
                'name'          => 'Illuminance Errors',
                'presentation'  => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                ],
            ],
        ],
        'presencezone' => [
            'value' => [
                'type'         => VARIABLETYPE_BOOLEAN,
                'name'         => 'Zone Presence',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_SWITCH,
                ],
            ],
            'num_objects' => [
                'type'         => VARIABLETYPE_INTEGER,
                'name'         => 'Objects in Zone',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                ],
            ],
        ],
        // ############################################################
        // ### TEST / EXPERIMENTELL - Dynamisch angelegte Komponenten ###
        // ############################################################
        // Shelly "User-defined components" (in der Shelly-Weboberfläche unter "User-defined
        // components" anlegbar, z.B. ein Boolean-Toggle oder ein Number-Wert). Werden je nach
        // Typ mit fortlaufender ID ab 200 erzeugt (z.B. "boolean:200", "number:201", ...).
        //
        // WICHTIG: Diese Komponenten tauchen NICHT in Shelly.GetStatus und NICHT in
        // Shelly.GetConfig auf - nur in Shelly.GetComponents (mit echten Geräten verifiziert,
        // siehe Chat-Verlauf). Deshalb reicht der normale Discovery-Weg (getComponentsViaStatus())
        // hier nicht aus, siehe ShellyModuleBase::getDynamicallyAddedComponents() (in
        // ComponentDefinitionHelper.php) und den ReceiveData()-Merge in ShellyModuleBase.php.
        //
        // Beispiel für einen Eintrag aus dem "components"-Array von Shelly.GetComponents:
        //   {
        //     "key": "boolean:200",
        //     "status": {"value": false, "source": "", "last_update_ts": 0},
        //     "config": {"id": 200, "name": "Test", "meta": {...}, "persisted": false, ...}
        //   }
        // "status.value" landet (via getDynamicallyAddedComponents()) hier unter dem Key-Pfad
        // "boolean.value" -> passt zum 'value'-Eintrag unten. "config.name" (und bei Enum
        // "config.options") wird NICHT hier statisch hinterlegt, weil er pro Gerät vom Nutzer
        // frei vergeben wird - stattdessen zur Laufzeit aus Shelly.GetComponents aufgelöst,
        // siehe ShellyModuleBase::getDynamicComponentMetadata() (nutzt Buffer
        // 'dynamicComponentsMetadata', befüllt in ReceiveData()).
        //
        // 'button' wird bewusst NICHT unterstützt: Buttons haben keinen persistenten Status
        // (status ist bei echten Geräten immer {}), sind also ein reiner Trigger statt eines
        // Werts - passt nicht zum bestehenden "Variable mit Wert + optionaler Aktion"-Modell.
        'boolean' => [
            'value' => [
                'type'         => VARIABLETYPE_BOOLEAN,
                'name'         => 'Boolean',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_SWITCH,
                ],
                'action'        => [
                    'method' => 'Boolean.Set',
                    'params' => ['id' => '', 'value' => '']
                ],
            ],
        ],
        'number' => [
            'value' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'Number',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                ],
                'action'        => [
                    'method' => 'Number.Set',
                    'params' => ['id' => '', 'value' => '']
                ],
            ],
        ],
        'enum' => [
            'value' => [
                'type'         => VARIABLETYPE_STRING,
                'name'         => 'Enum',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_ENUMERATION,
                    'OPTIONS'      => '[]',
                ],
                'action'        => [
                    'method' => 'Enum.Set',
                    'params' => ['id' => '', 'value' => '']
                ],
            ],
        ],
        'text' => [
            'value' => [
                'type'         => VARIABLETYPE_STRING,
                'name'         => 'Text',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                ],
                'action'        => [
                    'method' => 'Text.Set',
                    'params' => ['id' => '', 'value' => '']
                ],
            ],
        ],
        // ### ENDE TEST / EXPERIMENTELL ###############################
    ];
}
