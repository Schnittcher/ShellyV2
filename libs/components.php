<?php

declare(strict_types=1);
trait Components
{
    public static $components = [
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
                            "Color": 65280
                        },
                        {
                            "Value": false,
                            "Caption": "No alarm",
                            "IconActive": false,
                            "Icon": "",
                            "ColorActive": true,
                            "Color": 16711680
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
                                "Color": 65280
                            },
                            {
                                "Value": "DimDown",
                                "Caption": "Dim down",
                                "IconActive": false,
                                "Icon": "",
                                "Color": 16753920
                            },
                            {
                                "Value": "DimStop",
                                "Caption": "Dim stop",
                                "IconActive": false,
                                "Icon": "",
                                "Color": 16711680
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
                                "Color": 65280
                            },
                            {
                                "Value": "DimDown",
                                "Caption": "Dim down",
                                "IconActive": false,
                                "Icon": "",
                                "Color": 16753920
                            },
                            {
                                "Value": "DimStop",
                                "Caption": "Dim stop",
                                "IconActive": false,
                                "Icon": "",
                                "Color": 16711680
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
                                "Color": 65280
                            },
                            {
                                "Value": "DimDown",
                                "Caption": "Dim down",
                                "IconActive": false,
                                "Icon": "",
                                "Color": 16753920
                            },
                            {
                                "Value": "DimStop",
                                "Caption": "Dim stop",
                                "IconActive": false,
                                "Icon": "",
                                "Color": 16711680
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
                            "Color": 65280
                        },
                        {
                            "Value": "stopped",
                            "Caption": "Stopped",
                            "IconActive": false,
                            "Icon": "",
                            "Color": 16753920
                        },
                        {
                            "Value": "closing",
                            "Caption": "Closing",
                            "IconActive": false,
                            "Icon": "",
                            "Color": 16711680
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
                                "Color": 65280
                            },
                            {
                                "Value": "stop",
                                "Caption": "Stop",
                                "IconActive": false,
                                "Icon": "",
                                "Color": 16753920
                            },
                            {
                                "Value": "close",
                                "Caption": "Close",
                                "IconActive": false,
                                "Icon": "",
                                "Color": 16711680
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
                            "Color": 65280
                        },
                        {
                            "Value": false,
                            "Caption": "No smoke",
                            "IconActive": false,
                            "Icon": "",
                            "ColorActive": true,
                            "Color": 16711680
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
        ]
    ];
}