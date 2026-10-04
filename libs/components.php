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
                            "IconValue": "",
                            "ColorActive": true,
                            "ColorValue": 65280,
                            "ContentColorActive": false,
                            "ContentColorValue": -1
                        },
                        {
                            "Value": false,
                            "Caption": "No alarm",
                            "IconActive": false,
                            "IconValue": "",
                            "ColorActive": true,
                            "ColorValue": 16711680,
                            "ContentColorActive": false,
                            "ContentColorValue": -1
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
                                "IconValue": "",
                                "Color": 65280
                            },
                            {
                                "Value": "DimDown",
                                "Caption": "Dim down",
                                "IconActive": false,
                                "IconValue": "",
                                "Color": 16753920
                            },
                            {
                                "Value": "DimStop",
                                "Caption": "Dim stop",
                                "IconActive": false,
                                "IconValue": "",
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
                                "IconValue": "",
                                "Color": 65280
                            },
                            {
                                "Value": "DimDown",
                                "Caption": "Dim down",
                                "IconActive": false,
                                "IconValue": "",
                                "Color": 16753920
                            },
                            {
                                "Value": "DimStop",
                                "Caption": "Dim stop",
                                "IconActive": false,
                                "IconValue": "",
                                "Color": 16711680
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
                                "IconValue": "",
                                "Color": 65280
                            },
                            {
                                "Value": "DimDown",
                                "Caption": "Dim down",
                                "IconActive": false,
                                "IconValue": "",
                                "Color": 16753920
                            },
                            {
                                "Value": "DimStop",
                                "Caption": "Dim stop",
                                "IconActive": false,
                                "IconValue": "",
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
                                "IconValue": "",
                                "Color": 65280
                            },
                            {
                                "Value": "DimDown",
                                "Caption": "Dim down",
                                "IconActive": false,
                                "IconValue": "",
                                "Color": 16753920
                            },
                            {
                                "Value": "DimStop",
                                "Caption": "Dim stop",
                                "IconActive": false,
                                "IconValue": "",
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
        //RGBCCT (z.B. Shelly RGBCCT Bulb G3, Duo Bulb G3): RGB und Weißtemperatur in einer Komponente, Felder
        //laut Shelly-API-Doku (rgbcct:N). Die Doku nennt kein ct_range - MIN/MAX sind Standardwerte und werden
        //durch das ct_range der Geräte-Config überschrieben, falls das Gerät eines liefert.
        'rgbcct' => [
            'output' => [
                'type'         => VARIABLETYPE_BOOLEAN,
                'name'         => 'RGBCCT State',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_SWITCH,
                ],
                'action'        => [
                    'method' => 'RGBCCT.Set',
                    'params' => ['id' => '', 'on' => ''
                    ]
                ],
            ],
            'mode' => [
                'type'         => VARIABLETYPE_STRING,
                'name'         => 'RGBCCT Mode',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_ENUMERATION,
                    'OPTIONS'      => '[
                        {
                            "Value": "rgb",
                            "Caption": "RGB",
                            "IconActive": false,
                            "IconValue": "",
                            "Color": -1
                        },
                        {
                            "Value": "cct",
                            "Caption": "CCT",
                            "IconActive": false,
                            "IconValue": "",
                            "Color": -1
                        }
                    ]',
                ],
                'action'        => [
                    'method' => 'RGBCCT.Set',
                    'params' => ['id' => '', 'mode' => ''
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
                    'method' => 'RGBCCT.Set',
                    'params' => ['id' => '', 'rgb' => ''
                    ]
                ],
            ],
            'ct' => [
                'type'         => VARIABLETYPE_INTEGER,
                'name'         => 'RGBCCT color temperature',
                'presentation' => [
                    'PRESENTATION'  => VARIABLE_PRESENTATION_SLIDER,
                    'GRADIENT_TYPE' => 2,
                    'USAGE_TYPE'    => 1,
                    'PERCENTAGE'    => false,
                    'MIN'           => 2700,
                    'MAX'           => 6500,
                    'SUFFIX'        => ' K'
                ],
                'action'        => [
                    'method' => 'RGBCCT.Set',
                    'params' => ['id' => '', 'ct' => ''
                    ]
                ],
            ],
            'brightness' => [
                'type'         => VARIABLETYPE_INTEGER,
                'name'         => 'RGBCCT Brightness',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_SLIDER,
                    'SUFFIX'       => ' %',
                    'USAGE_TYPE'   => 2
                ],
                'action'        => [
                    'method' => 'RGBCCT.Set',
                    'params' => ['id' => '', 'brightness' => ''
                    ]
                ],
                'actionWithExtraVariable' => [
                    'type'         => VARIABLETYPE_STRING,
                    'name'         => 'RGBCCT Brightness Action',
                    'presentation' => [
                        'PRESENTATION' => VARIABLE_PRESENTATION_ENUMERATION,
                        'ICON'         => 'Light',
                        'LAYOUT'       => 1,
                        'OPTIONS'      => '[
                            {
                                "Value": "DimUp",
                                "Caption": "Dim up",
                                "IconActive": false,
                                "IconValue": "",
                                "Color": 65280
                            },
                            {
                                "Value": "DimDown",
                                "Caption": "Dim down",
                                "IconActive": false,
                                "IconValue": "",
                                "Color": 16753920
                            },
                            {
                                "Value": "DimStop",
                                "Caption": "Dim stop",
                                "IconActive": false,
                                "IconValue": "",
                                "Color": 16711680
                            }
                        ]',
                    ],
                    'action'        => [
                        'list'   => true,
                        'method' => 'RGBCCT.',
                        'params' => ['id' => ''
                        ]
                    ],
                ],
            ],
            'apower' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'RGBCCT active power',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' W',
                ],
            ],
            'aenergy' => [
                'total' => [
                    'type'         => VARIABLETYPE_FLOAT,
                    'name'         => 'RGBCCT Total energy',
                    'factor'       => 0.001,
                    'presentation' => [
                        'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                        'SUFFIX'       => ' kWh'
                    ],
                ],
            ],
        ],
        'blutrv' => [
            'current_C' => [
                'type'         => VARIABLETYPE_FLOAT,
                //Die am Thermostat hinterlegte externe Temperatur (TRV.SetExternalTemperature), nicht die gemessene.
                'name'         => 'External temperature',
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
        //CB (Shelly Pro 3CB, Leitungsschutzschalter): Felder laut Shelly-API-Doku (cb:N). Laut Doku kennt CB.Set
        //nur "output": false (Hebel auslösen) - ein Einschalten aus der Ferne ist nicht dokumentiert, die
        //Schalter-Variable löst also zuverlässig nur aus. Bei gesperrtem Sicherheitsschalter ("safety") ist
        //das Schalten aus der Ferne deaktiviert.
        'cb' => [
            'output' => [
                'type'         => VARIABLETYPE_BOOLEAN,
                'name'         => 'Breaker State',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_SWITCH,
                ],
                'action'        => [
                    'method' => 'CB.Set',
                    'params' => ['id' => '', 'output' => ''
                    ]
                ],
            ],
            'safety' => [
                'type'         => VARIABLETYPE_BOOLEAN,
                'name'         => 'Safety lock',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'OPTIONS'      => '[
                        {
                            "Value": true,
                            "Caption": "Locked",
                            "IconActive": false,
                            "IconValue": "",
                            "ColorActive": true,
                            "ColorValue": 16711680,
                            "ContentColorActive": false,
                            "ContentColorValue": -1
                        },
                        {
                            "Value": false,
                            "Caption": "Unlocked",
                            "IconActive": false,
                            "IconValue": "",
                            "ColorActive": true,
                            "ColorValue": 65280,
                            "ContentColorActive": false,
                            "ContentColorValue": -1
                        }
                    ]',
                ],
            ],
            'total_cycles' => [
                'type'         => VARIABLETYPE_INTEGER,
                'name'         => 'Total cycles',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
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
        //Cury (Shelly Cury, Duftspender): Felder laut Shelly-API-Doku (cury:N). Die Fächer "slots.left/right"
        //sind null, solange kein Fläschchen steckt - die Blätter werden deshalb in createVariableListForForm()
        //fest ergänzt. Der "slot" steht bei den Aktionen hinter dem Wertparameter, weil RequestAction() den
        //zweiten Parameter mit dem Wert überschreibt. Der Boost läuft als Extra-Variable mit 'list' (Methode
        //"Cury." + Boost/StopBoost). Nicht abgebildet: boost/timer (Zeitstempel), Fläschchenfarbe, vial_fault
        //(laut Doku uneinheitlich verortet; Fehler stehen auch in "errors").
        'cury' => [
            'mode' => [
                'type'         => VARIABLETYPE_STRING,
                'name'         => 'Room mode',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_ENUMERATION,
                    'OPTIONS'      => '[
                        {
                            "Value": "hall",
                            "Caption": "Hall",
                            "IconActive": false,
                            "IconValue": "",
                            "Color": -1
                        },
                        {
                            "Value": "bedroom",
                            "Caption": "Bedroom",
                            "IconActive": false,
                            "IconValue": "",
                            "Color": -1
                        },
                        {
                            "Value": "living_room",
                            "Caption": "Living room",
                            "IconActive": false,
                            "IconValue": "",
                            "Color": -1
                        },
                        {
                            "Value": "lavatory_room",
                            "Caption": "Lavatory",
                            "IconActive": false,
                            "IconValue": "",
                            "Color": -1
                        },
                        {
                            "Value": "reception",
                            "Caption": "Reception",
                            "IconActive": false,
                            "IconValue": "",
                            "Color": -1
                        },
                        {
                            "Value": "workplace",
                            "Caption": "Workplace",
                            "IconActive": false,
                            "IconValue": "",
                            "Color": -1
                        }
                    ]',
                ],
                'action'        => [
                    'method' => 'Cury.SetMode',
                    'params' => ['id' => '', 'mode' => ''
                    ]
                ],
            ],
            'away_mode' => [
                'type'         => VARIABLETYPE_BOOLEAN,
                'name'         => 'Away mode',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_SWITCH,
                ],
                'action'        => [
                    'method' => 'Cury.SetAwayMode',
                    'params' => ['id' => '', 'on' => ''
                    ]
                ],
            ],
            'slots' => [
                'left' => [
                    'on' => [
                        'type'         => VARIABLETYPE_BOOLEAN,
                        'name'         => 'Left slot state',
                        'alwaysCreate' => true,
                        'presentation' => [
                            'PRESENTATION' => VARIABLE_PRESENTATION_SWITCH,
                        ],
                        'action'        => [
                            'method' => 'Cury.Set',
                            'params' => ['id' => '', 'on' => '', 'slot' => 'left'
                            ]
                        ],
                        'actionWithExtraVariable' => [
                            'type'         => VARIABLETYPE_STRING,
                            'name'         => 'Left slot boost',
                            'presentation' => [
                                'PRESENTATION' => VARIABLE_PRESENTATION_ENUMERATION,
                                'LAYOUT'       => 1,
                                'OPTIONS'      => '[
                                    {
                                        "Value": "Boost",
                                        "Caption": "Boost",
                                        "IconActive": false,
                                        "IconValue": "",
                                        "Color": 65280
                                    },
                                    {
                                        "Value": "StopBoost",
                                        "Caption": "Stop boost",
                                        "IconActive": false,
                                        "IconValue": "",
                                        "Color": 16711680
                                    }
                                ]',
                            ],
                            'action'        => [
                                'list'   => true,
                                'method' => 'Cury.',
                                'params' => ['id' => '', 'slot' => 'left'
                                ]
                            ],
                        ],
                    ],
                    'intensity' => [
                        'type'         => VARIABLETYPE_INTEGER,
                        'name'         => 'Left slot intensity',
                        'alwaysCreate' => true,
                        'presentation' => [
                            'PRESENTATION' => VARIABLE_PRESENTATION_SLIDER,
                            'MIN'          => 0,
                            'MAX'          => 100,
                            'SUFFIX'       => ' %',
                            'USAGE_TYPE'   => 5
                        ],
                        'action'        => [
                            'method' => 'Cury.Set',
                            'params' => ['id' => '', 'intensity' => '', 'slot' => 'left'
                            ]
                        ],
                    ],
                    'vial' => [
                        'level' => [
                            'type'         => VARIABLETYPE_INTEGER,
                            'name'         => 'Left vial level',
                            'alwaysCreate' => true,
                            'presentation' => [
                                'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                                'SUFFIX'       => ' %'
                            ],
                        ],
                        'name' => [
                            'type'         => VARIABLETYPE_STRING,
                            'name'         => 'Left vial name',
                            'alwaysCreate' => true,
                            'presentation' => [
                                'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                            ],
                        ],
                        'serial' => [
                            'type'         => VARIABLETYPE_STRING,
                            'name'         => 'Left vial serial',
                            'alwaysCreate' => true,
                            'presentation' => [
                                'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                            ],
                        ],
                    ],
                ],
                'right' => [
                    'on' => [
                        'type'         => VARIABLETYPE_BOOLEAN,
                        'name'         => 'Right slot state',
                        'alwaysCreate' => true,
                        'presentation' => [
                            'PRESENTATION' => VARIABLE_PRESENTATION_SWITCH,
                        ],
                        'action'        => [
                            'method' => 'Cury.Set',
                            'params' => ['id' => '', 'on' => '', 'slot' => 'right'
                            ]
                        ],
                        'actionWithExtraVariable' => [
                            'type'         => VARIABLETYPE_STRING,
                            'name'         => 'Right slot boost',
                            'presentation' => [
                                'PRESENTATION' => VARIABLE_PRESENTATION_ENUMERATION,
                                'LAYOUT'       => 1,
                                'OPTIONS'      => '[
                                    {
                                        "Value": "Boost",
                                        "Caption": "Boost",
                                        "IconActive": false,
                                        "IconValue": "",
                                        "Color": 65280
                                    },
                                    {
                                        "Value": "StopBoost",
                                        "Caption": "Stop boost",
                                        "IconActive": false,
                                        "IconValue": "",
                                        "Color": 16711680
                                    }
                                ]',
                            ],
                            'action'        => [
                                'list'   => true,
                                'method' => 'Cury.',
                                'params' => ['id' => '', 'slot' => 'right'
                                ]
                            ],
                        ],
                    ],
                    'intensity' => [
                        'type'         => VARIABLETYPE_INTEGER,
                        'name'         => 'Right slot intensity',
                        'alwaysCreate' => true,
                        'presentation' => [
                            'PRESENTATION' => VARIABLE_PRESENTATION_SLIDER,
                            'MIN'          => 0,
                            'MAX'          => 100,
                            'SUFFIX'       => ' %',
                            'USAGE_TYPE'   => 5
                        ],
                        'action'        => [
                            'method' => 'Cury.Set',
                            'params' => ['id' => '', 'intensity' => '', 'slot' => 'right'
                            ]
                        ],
                    ],
                    'vial' => [
                        'level' => [
                            'type'         => VARIABLETYPE_INTEGER,
                            'name'         => 'Right vial level',
                            'alwaysCreate' => true,
                            'presentation' => [
                                'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                                'SUFFIX'       => ' %'
                            ],
                        ],
                        'name' => [
                            'type'         => VARIABLETYPE_STRING,
                            'name'         => 'Right vial name',
                            'alwaysCreate' => true,
                            'presentation' => [
                                'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                            ],
                        ],
                        'serial' => [
                            'type'         => VARIABLETYPE_STRING,
                            'name'         => 'Right vial serial',
                            'alwaysCreate' => true,
                            'presentation' => [
                                'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                            ],
                        ],
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
        //Camera (Shelly Camera, Key camera:N): Status und Aktionen laut Shelly-API-Doku, am Gerät S1CM-0DXW00
        //(Firmware 2.1.99) geprüft. Einträge mit 'configPath' kommen nicht aus dem Status, sondern aus der
        //Geräte-Config (z.B. rtsp.enable): sie werden beim Einlesen in den Status gespiegelt
        //(mergeConfigBackedValues()) und per Camera.SetConfig mit verschachtelter Config gesetzt
        //(RequestAction()). Die Gruppennamen (rtsp, led, ...) sind frei gewählt und enthalten bewusst keinen
        //Unterstrich, weil Idents an Unterstrichen zerlegt werden. 'recordings' (UUID als Schlüssel) wird nicht
        //abgebildet. Die Schnappschuss-Aufnahme liefert nur eine media_id (Bild liegt in der Shelly-Cloud/auf der
        //SD-Karte), der Live-Stream läuft über das Stream-Objekt der Instanz (siehe ShellyComponent).
        'camera' => [
            'arm' => [
                'type'         => VARIABLETYPE_BOOLEAN,
                'name'         => 'Camera armed',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_SWITCH,
                ],
                'action'        => [
                    'method' => 'Camera.Set',
                    'params' => ['id' => '', 'arm' => ''
                    ]
                ],
                'actionWithExtraVariable' => [
                    'type'         => VARIABLETYPE_STRING,
                    'name'         => 'Camera action',
                    'presentation' => [
                        'PRESENTATION' => VARIABLE_PRESENTATION_ENUMERATION,
                        'LAYOUT'       => 1,
                        'OPTIONS'      => '[
                            {
                                "Value": "CaptureImage",
                                "Caption": "Snapshot",
                                "IconActive": false,
                                "IconValue": "",
                                "Color": -1
                            },
                            {
                                "Value": "StartRecording",
                                "Caption": "Start recording",
                                "IconActive": false,
                                "IconValue": "",
                                "Color": 65280
                            },
                            {
                                "Value": "StopRecording",
                                "Caption": "Stop recording",
                                "IconActive": false,
                                "IconValue": "",
                                "Color": 16711680
                            }
                        ]',
                    ],
                    'action'        => [
                        'list'   => true,
                        'method' => 'Camera.',
                        'params' => ['id' => ''
                        ]
                    ],
                ],
            ],
            'privacy' => [
                'type'         => VARIABLETYPE_BOOLEAN,
                'name'         => 'Camera privacy',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_SWITCH,
                ],
                'action'        => [
                    'method' => 'Camera.Set',
                    'params' => ['id' => '', 'privacy' => ''
                    ]
                ],
            ],
            'motion' => [
                'type'         => VARIABLETYPE_BOOLEAN,
                'name'         => 'Camera motion',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'OPTIONS'      => '[
                        {
                            "Value": true,
                            "Caption": "Motion",
                            "IconActive": false,
                            "IconValue": "",
                            "ColorActive": true,
                            "ColorValue": 16753920,
                            "ContentColorActive": false,
                            "ContentColorValue": -1
                        },
                        {
                            "Value": false,
                            "Caption": "No motion",
                            "IconActive": false,
                            "IconValue": "",
                            "ColorActive": false,
                            "ColorValue": -1,
                            "ContentColorActive": false,
                            "ContentColorValue": -1
                        }
                    ]',
                ],
            ],
            'streamer' => [
                'type'         => VARIABLETYPE_STRING,
                'name'         => 'Camera streamer',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                ],
            ],
            'streams' => [
                'type'         => VARIABLETYPE_INTEGER,
                'name'         => 'Camera streams',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                ],
            ],
            //Kein Statusfeld - nur zum Auslösen von Camera.PlaySound (Töne müssen am Gerät aktiviert sein und
            //der Privatsphäre-Modus aus), deshalb 'alwaysCreate'.
            'playsound' => [
                'type'         => VARIABLETYPE_STRING,
                'name'         => 'Camera sound',
                'alwaysCreate' => true,
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_ENUMERATION,
                    'OPTIONS'      => '[
                        {
                            "Value": "alert",
                            "Caption": "Alert",
                            "IconActive": false,
                            "IconValue": "",
                            "Color": -1
                        },
                        {
                            "Value": "ding-dong",
                            "Caption": "Ding-dong",
                            "IconActive": false,
                            "IconValue": "",
                            "Color": -1
                        },
                        {
                            "Value": "notification",
                            "Caption": "Notification",
                            "IconActive": false,
                            "IconValue": "",
                            "Color": -1
                        }
                    ]',
                ],
                'action'        => [
                    'method' => 'Camera.PlaySound',
                    'params' => ['id' => '', 'sound' => ''
                    ]
                ],
            ],
            'errors' => [
                'type'         => VARIABLETYPE_STRING,
                'name'         => 'Errors',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                ],
            ],
            'rtsp' => [
                'enable' => [
                    'type'         => VARIABLETYPE_BOOLEAN,
                    'name'         => 'Camera RTSP',
                    'configPath'   => 'rtsp.enable',
                    'presentation' => [
                        'PRESENTATION' => VARIABLE_PRESENTATION_SWITCH,
                    ],
                    'action'        => [
                        'method' => 'Camera.SetConfig',
                        'params' => ['id' => '', 'config' => ''
                        ]
                    ],
                ],
            ],
            'led' => [
                'enable' => [
                    'type'         => VARIABLETYPE_BOOLEAN,
                    'name'         => 'Camera LED',
                    'configPath'   => 'led.enable',
                    'presentation' => [
                        'PRESENTATION' => VARIABLE_PRESENTATION_SWITCH,
                    ],
                    'action'        => [
                        'method' => 'Camera.SetConfig',
                        'params' => ['id' => '', 'config' => ''
                        ]
                    ],
                ],
            ],
            'sounds' => [
                'enable' => [
                    'type'         => VARIABLETYPE_BOOLEAN,
                    'name'         => 'Camera sounds',
                    'configPath'   => 'sounds.enable',
                    'presentation' => [
                        'PRESENTATION' => VARIABLE_PRESENTATION_SWITCH,
                    ],
                    'action'        => [
                        'method' => 'Camera.SetConfig',
                        'params' => ['id' => '', 'config' => ''
                        ]
                    ],
                ],
            ],
            'motionrecording' => [
                'enable' => [
                    'type'         => VARIABLETYPE_BOOLEAN,
                    'name'         => 'Camera motion recording',
                    'configPath'   => 'motion.recording.enable',
                    'presentation' => [
                        'PRESENTATION' => VARIABLE_PRESENTATION_SWITCH,
                    ],
                    'action'        => [
                        'method' => 'Camera.SetConfig',
                        'params' => ['id' => '', 'config' => ''
                        ]
                    ],
                ],
            ],
            'sensitivity' => [
                'level' => [
                    'type'         => VARIABLETYPE_STRING,
                    'name'         => 'Camera motion sensitivity',
                    'configPath'   => 'motion.sensitivity',
                    'presentation' => [
                        'PRESENTATION' => VARIABLE_PRESENTATION_ENUMERATION,
                        'OPTIONS'      => '[
                            {
                                "Value": "low",
                                "Caption": "Low",
                                "IconActive": false,
                                "IconValue": "",
                                "Color": -1
                            },
                            {
                                "Value": "medium",
                                "Caption": "Medium",
                                "IconActive": false,
                                "IconValue": "",
                                "Color": -1
                            },
                            {
                                "Value": "high",
                                "Caption": "High",
                                "IconActive": false,
                                "IconValue": "",
                                "Color": -1
                            }
                        ]',
                    ],
                    'action'        => [
                        'method' => 'Camera.SetConfig',
                        'params' => ['id' => '', 'config' => ''
                        ]
                    ],
                ],
            ],
            'nightvision' => [
                'mode' => [
                    'type'         => VARIABLETYPE_STRING,
                    'name'         => 'Camera night vision',
                    'configPath'   => 'night_vision.mode',
                    'presentation' => [
                        'PRESENTATION' => VARIABLE_PRESENTATION_ENUMERATION,
                        'OPTIONS'      => '[
                            {
                                "Value": "auto",
                                "Caption": "Auto",
                                "IconActive": false,
                                "IconValue": "",
                                "Color": -1
                            },
                            {
                                "Value": "day",
                                "Caption": "Day",
                                "IconActive": false,
                                "IconValue": "",
                                "Color": -1
                            },
                            {
                                "Value": "night",
                                "Caption": "Night",
                                "IconActive": false,
                                "IconValue": "",
                                "Color": -1
                            }
                        ]',
                    ],
                    'action'        => [
                        'method' => 'Camera.SetConfig',
                        'params' => ['id' => '', 'config' => ''
                        ]
                    ],
                ],
            ],
        ],
        //Kamerazone (camerazone:N, ab ID 200): Status "motion" gibt es nur bei Zonen vom Typ "motion". Der Zonenname
        //aus der Config wird wie bei presencezone dem Variablennamen vorangestellt.
        'camerazone' => [
            'motion' => [
                'type'         => VARIABLETYPE_BOOLEAN,
                'name'         => 'Zone motion',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'OPTIONS'      => '[
                        {
                            "Value": true,
                            "Caption": "Motion",
                            "IconActive": false,
                            "IconValue": "",
                            "ColorActive": true,
                            "ColorValue": 16753920,
                            "ContentColorActive": false,
                            "ContentColorValue": -1
                        },
                        {
                            "Value": false,
                            "Caption": "No motion",
                            "IconActive": false,
                            "IconValue": "",
                            "ColorActive": false,
                            "ColorValue": -1,
                            "ContentColorActive": false,
                            "ContentColorValue": -1
                        }
                    ]',
                ],
            ],
        ],
        //DALI (Shelly DALI Dimmer Gen3, Key "dali" ohne Nummer): Felder laut Shelly-API-Doku. Gedimmt wird über
        //light:0. cg_count = Anzahl der beim letzten Scan gefundenen Vorschaltgeräte (null, solange nie
        //gescannt - dann behält die Variable ihren Wert). Das Objekt "scan" gibt es nur WÄHREND eines Scans,
        //deshalb 'alwaysCreate'. DALI.StartScan/DALI.PingKnownDevices haben keine Parameter ('params' => []).
        //Das Ergebnis eines Scans meldet das Gerät zusätzlich als Event (scan_complete/ping_complete).
        'dali' => [
            'cg_count' => [
                'type'         => VARIABLETYPE_INTEGER,
                'name'         => 'DALI control gears',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                ],
                'actionWithExtraVariable' => [
                    'type'         => VARIABLETYPE_STRING,
                    'name'         => 'DALI action',
                    'presentation' => [
                        'PRESENTATION' => VARIABLE_PRESENTATION_ENUMERATION,
                        'LAYOUT'       => 1,
                        'OPTIONS'      => '[
                            {
                                "Value": "StartScan",
                                "Caption": "Start scan",
                                "IconActive": false,
                                "IconValue": "",
                                "Color": 65280
                            },
                            {
                                "Value": "PingKnownDevices",
                                "Caption": "Check devices",
                                "IconActive": false,
                                "IconValue": "",
                                "Color": -1
                            }
                        ]',
                    ],
                    'action'        => [
                        'list'   => true,
                        'method' => 'DALI.',
                        'params' => []
                    ],
                ],
            ],
            'scan' => [
                'cg_count' => [
                    'type'         => VARIABLETYPE_INTEGER,
                    'name'         => 'DALI scan control gears',
                    'alwaysCreate' => true,
                    'presentation' => [
                        'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    ],
                ],
                'errors' => [
                    'type'         => VARIABLETYPE_STRING,
                    'name'         => 'DALI scan errors',
                    'alwaysCreate' => true,
                    'presentation' => [
                        'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    ],
                ],
            ],
        ],
        //BTHome (Shelly BLU-Geräte an einem Gateway): bthomedevice:2xx ist das BLU-Gerät, bthomesensor:2xx je ein
        //Messwert. Alles nur lesend. Der Dienst-Status 'bthome' (z.B. bluetooth_disabled) ist bewusst NICHT definiert,
        //weil er sonst bei jedem Gen3-Gerät mit Bluetooth als Variable erscheinen würde. Der Typ von
        //'bthomesensor.value' (Zahl/Boolean/Text), sein Name und die Einheit werden zur Laufzeit bestimmt (siehe
        //libs/BTHomeObjects.php); die Definition hier ist nur der Standard. Felder am echten BLU Gateway G3 geprüft
        //(04.10.2026): Zeitstempel heißt last_updated_ts, value kommt schon umgerechnet (z.B. 22.1 °C).
        'bthomedevice' => [
            'rssi' => [
                'type'         => VARIABLETYPE_INTEGER,
                'name'         => 'RSSI',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' dBm',
                ],
            ],
            'battery' => [
                'type'         => VARIABLETYPE_INTEGER,
                'name'         => 'Battery',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' %',
                ],
            ],
            'last_updated_ts' => [
                'type'         => VARIABLETYPE_INTEGER,
                'name'         => 'Last update',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_DATE_TIME,
                    'DATE'         => 1,
                    'TIME'         => 2,
                ],
            ],
            'paired' => [
                'type'         => VARIABLETYPE_BOOLEAN,
                'name'         => 'Paired',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'OPTIONS'      => '[
                        {
                            "Value": true,
                            "Caption": "Paired",
                            "IconActive": false,
                            "IconValue": "",
                            "ColorActive": false,
                            "ColorValue": -1,
                            "ContentColorActive": false,
                            "ContentColorValue": -1
                        },
                        {
                            "Value": false,
                            "Caption": "Not paired",
                            "IconActive": false,
                            "IconValue": "",
                            "ColorActive": true,
                            "ColorValue": 16711680,
                            "ContentColorActive": false,
                            "ContentColorValue": -1
                        }
                    ]',
                ],
            ],
            'key' => [
                'type'         => VARIABLETYPE_BOOLEAN,
                'name'         => 'Encryption key set',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'OPTIONS'      => '[
                        {
                            "Value": true,
                            "Caption": "Yes",
                            "IconActive": false,
                            "IconValue": "",
                            "ColorActive": false,
                            "ColorValue": -1,
                            "ContentColorActive": false,
                            "ContentColorValue": -1
                        },
                        {
                            "Value": false,
                            "Caption": "No",
                            "IconActive": false,
                            "IconValue": "",
                            "ColorActive": false,
                            "ColorValue": -1,
                            "ContentColorActive": false,
                            "ContentColorValue": -1
                        }
                    ]',
                ],
            ],
            //Letzter Tastendruck (single_push, double_push, triple_push, ...): kommt nur als Ereignis (NotifyEvent), nicht im
            //Status. Nicht jedes BLU-Gerät hat eine Taste - die Variable wird nur für Geräte angelegt, bei denen man das erkennt,
            //sonst beim ersten Tastendruck (siehe bthomeDeviceShowsButton() in libs/BTHomeObjects.php). Die Darstellung mit den
            //übersetzten Tastendrücken setzt registerComponentVariables().
            'button' => [
                'type'         => VARIABLETYPE_STRING,
                'name'         => 'Button',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                ],
            ],
            'fw_ver' => [
                'type'         => VARIABLETYPE_STRING,
                'name'         => 'Firmware version',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
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
        'bthomesensor' => [
            'value' => [
                'type'         => VARIABLETYPE_FLOAT,
                'name'         => 'BTHome value',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                ],
            ],
            'last_updated_ts' => [
                'type'         => VARIABLETYPE_INTEGER,
                'name'         => 'Last update',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_DATE_TIME,
                    'DATE'         => 1,
                    'TIME'         => 2,
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
                            "IconValue": "",
                            "Color": 65280
                        },
                        {
                            "Value": "stopped",
                            "Caption": "Stopped",
                            "IconActive": false,
                            "IconValue": "",
                            "Color": 16753920
                        },
                        {
                            "Value": "closing",
                            "Caption": "Closing",
                            "IconActive": false,
                            "IconValue": "",
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
                                "IconValue": "",
                                "Color": 65280
                            },
                            {
                                "Value": "stop",
                                "Caption": "Stop",
                                "IconActive": false,
                                "IconValue": "",
                                "Color": 16753920
                            },
                            {
                                "Value": "close",
                                "Caption": "Close",
                                "IconActive": false,
                                "IconValue": "",
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
                            "IconValue": "",
                            "ColorActive": true,
                            "ColorValue": 65280,
                            "ContentColorActive": false,
                            "ContentColorValue": -1
                        },
                        {
                            "Value": false,
                            "Caption": "No smoke",
                            "IconActive": false,
                            "IconValue": "",
                            "ColorActive": true,
                            "ColorValue": 16711680,
                            "ContentColorActive": false,
                            "ContentColorValue": -1
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
                            "IconValue": "",
                            "Color": 16711680
                        },
                        {
                            "Value": "twilight",
                            "Caption": "Twilight",
                            "IconActive": false,
                            "IconValue": "",
                            "Color": 16753920
                        },
                        {
                            "Value": "bright",
                            "Caption": "Bright",
                            "IconActive": false,
                            "IconValue": "",
                            "Color": 65280
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
        // Shelly.GetConfig auf - nur in Shelly.GetComponents (mit echten Geräten verifiziert).
        // Deshalb liest ShellyModuleBase Status und Config aller Komponenten über
        // Shelly.GetComponents (requestComponentsStatus()).
        //
        // Beispiel für einen Eintrag aus dem "components"-Array von Shelly.GetComponents:
        //   {
        //     "key": "boolean:200",
        //     "status": {"value": false, "source": "", "last_update_ts": 0},
        //     "config": {"id": 200, "name": "Test", "meta": {...}, "persisted": false, ...}
        //   }
        // "status.value" landet hier unter dem Key-Pfad "boolean.value" -> passt zum
        // 'value'-Eintrag unten. "config.name" (und bei Enum "config.options") wird NICHT hier
        // statisch hinterlegt, weil er pro Gerät vom Nutzer frei vergeben wird - stattdessen zur
        // Laufzeit aus Shelly.GetComponents aufgelöst, siehe
        // ShellyModuleBase::getDynamicComponentMetadata() (nutzt Buffer 'componentConfigs',
        // befüllt in ReceiveData()).
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
                //VARIABLE_PRESENTATION_VALUE_PRESENTATION ist NUR für nicht-schreibbare Variablen
                //zulässig - da diese Komponente eine "action" hat (also EnableAction() aufgerufen
                //wird), muss eine schreibfähige Präsentation her. SLIDER passt zum bereits bestehenden
                //Min/Max/Einheit-Override aus den Geräte-Metadaten (siehe registerComponentVariables()).
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_SLIDER,
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
                //VARIABLE_PRESENTATION_VALUE_PRESENTATION ist NUR für nicht-schreibbare Variablen
                //zulässig - da diese Komponente eine "action" hat, muss eine schreibfähige Präsentation
                //her. VALUE_INPUT ist die Symcon-Präsentation für freien Text-Input.
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_INPUT,
                ],
                'action'        => [
                    'method' => 'Text.Set',
                    'params' => ['id' => '', 'value' => '']
                ],
            ],
        ],
        // 'object' ist KEIN generischer Skalar-Typ wie boolean/number/enum/text - "value" ist ein
        // verschachteltes Objekt, dessen Form je nach Gerät/Service völlig unterschiedlich sein kann
        // (hier: Energiemess-Objekt eines Shelly EV-Chargers, role "phase_info", mit echtem Gerät
        // verifiziert). Deshalb NICHT generisch, sondern als feste Unterstruktur abgebildet - passt
        // nur für object-Komponenten mit exakt dieser Form. Rein lesend (kein 'action'), da
        // "access": "cr" beim echten Gerät. "counter.minute_ts"/"counter.by_minute" werden bewusst
        // nicht abgebildet (Zeitstempel/Minuten-Array, keine sinnvollen Einzelvariablen).
        'object' => [
            'value' => [
                'counter' => [
                    'total' => [
                        'type'         => VARIABLETYPE_FLOAT,
                        'name'         => 'Total consumption',
                        'presentation' => [
                            'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                            'SUFFIX'       => ' kWh',
                        ],
                    ],
                ],
                'total_current' => [
                    'type'         => VARIABLETYPE_FLOAT,
                    'name'         => 'Total current',
                    'presentation' => [
                        'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                        'SUFFIX'       => ' A',
                    ],
                ],
                'total_power' => [
                    'type'         => VARIABLETYPE_FLOAT,
                    'name'         => 'Total power',
                    'presentation' => [
                        'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                        'SUFFIX'       => ' W',
                    ],
                ],
                'total_act_energy' => [
                    'type'         => VARIABLETYPE_FLOAT,
                    'name'         => 'Total active energy',
                    'presentation' => [
                        'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                        'SUFFIX'       => ' kWh',
                    ],
                ],
                'phase_a' => [
                    'voltage' => [
                        'type'         => VARIABLETYPE_FLOAT,
                        'name'         => 'Phase A voltage',
                        'presentation' => [
                            'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                            'SUFFIX'       => ' V',
                        ],
                    ],
                    'current' => [
                        'type'         => VARIABLETYPE_FLOAT,
                        'name'         => 'Phase A current',
                        'presentation' => [
                            'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                            'SUFFIX'       => ' A',
                        ],
                    ],
                    'power' => [
                        'type'         => VARIABLETYPE_FLOAT,
                        'name'         => 'Phase A power',
                        'presentation' => [
                            'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                            'SUFFIX'       => ' W',
                        ],
                    ],
                ],
                'phase_b' => [
                    'voltage' => [
                        'type'         => VARIABLETYPE_FLOAT,
                        'name'         => 'Phase B voltage',
                        'presentation' => [
                            'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                            'SUFFIX'       => ' V',
                        ],
                    ],
                    'current' => [
                        'type'         => VARIABLETYPE_FLOAT,
                        'name'         => 'Phase B current',
                        'presentation' => [
                            'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                            'SUFFIX'       => ' A',
                        ],
                    ],
                    'power' => [
                        'type'         => VARIABLETYPE_FLOAT,
                        'name'         => 'Phase B power',
                        'presentation' => [
                            'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                            'SUFFIX'       => ' W',
                        ],
                    ],
                ],
                'phase_c' => [
                    'voltage' => [
                        'type'         => VARIABLETYPE_FLOAT,
                        'name'         => 'Phase C voltage',
                        'presentation' => [
                            'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                            'SUFFIX'       => ' V',
                        ],
                    ],
                    'current' => [
                        'type'         => VARIABLETYPE_FLOAT,
                        'name'         => 'Phase C current',
                        'presentation' => [
                            'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                            'SUFFIX'       => ' A',
                        ],
                    ],
                    'power' => [
                        'type'         => VARIABLETYPE_FLOAT,
                        'name'         => 'Phase C power',
                        'presentation' => [
                            'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                            'SUFFIX'       => ' W',
                        ],
                    ],
                ],
            ],
        ],
        // ### ENDE TEST / EXPERIMENTELL ###############################
    ];
}
