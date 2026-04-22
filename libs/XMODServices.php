<?php

declare(strict_types=1);
trait XMODServices
{
    public static $services = [

        'linkedgo-st-802-hvac' => [
            'enable' => [
                'name'         => 'State',
                'type'         => VARIABLETYPE_BOOLEAN,
                'action'       => true,
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                ],
                'receive' => 'boolean:201'
            ],
            'current_temperature' => [
                'name'         => 'Current Temperature',
                'action'       => false,
                'type'         => VARIABLETYPE_FLOAT,
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' °C',
                    'MIN'          => 0,
                    'MAX'          => 100,
                ],
                'receive' => 'number:201'
            ],
            'target_temperature' => [
                'name'         => 'Target Temperature',
                'action'       => true,
                'type'         => VARIABLETYPE_FLOAT,
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' °C',
                    'MIN'          => 5,
                    'MAX'          => 30,
                ],
                'receive' => 'number:203'
            ],
            'fan_speed' => [
                'name'         => 'Fan Speed',
                'action'       => true,
                'type'         => VARIABLETYPE_STRING,
                'presentation' => [
                    'PRESENTATION'    => VARIABLE_PRESENTATION_ENUMERATION,
                    'ICON'            => 'fan',
                    'LAYOUT'          => 1,
                    'DISPLAY'         => 2,
                    'OPTIONS'         => [
                        [
                            'Value'                 => 'auto',
                            'Caption'               => 'Auto',
                            'IconActive'            => false,
                            'IconValue'             => 'info',
                            'Color'                 => 65280,
                        ],
                        [
                            'Value'                 => 'low',
                            'Caption'               => 'Low',
                            'IconActive'            => false,
                            'IconValue'             => 'info',
                            'Color'                 => 16711680,
                        ],
                        [
                            'Value'                 => 'medium',
                            'Caption'               => 'Medium',
                            'IconActive'            => false,
                            'IconValue'             => 'info',
                            'Color'                 => 16711680,
                        ],
                        [
                            'Value'                 => 'high',
                            'Caption'               => 'High',
                            'IconActive'            => false,
                            'IconValue'             => 'info',
                            'Color'                 => 16711680,
                        ],
                    ],
                ],
                'receive' => 'enum:200'
            ],
            'working_mode' => [
                'name'         => 'Mode',
                'action'       => true,
                'type'         => VARIABLETYPE_STRING,
                'presentation' => [
                    'PRESENTATION'    => VARIABLE_PRESENTATION_ENUMERATION,
                    'ICON'            => 'info',
                    'LAYOUT'          => 1,
                    'DISPLAY'         => 2,
                    'OPTIONS'         => [
                        [
                            'Value'                 => 'cool',
                            'Caption'               => 'Cool',
                            'IconActive'            => false,
                            'IconValue'             => 'info',
                            'Color'                 => 65280,
                        ],
                        [
                            'Value'                 => 'dry',
                            'Caption'               => 'Dry',
                            'IconActive'            => false,
                            'IconValue'             => 'info',
                            'Color'                 => 16711680,
                        ],
                        [
                            'Value'                 => 'floor_heating',
                            'Caption'               => 'Floor heating',
                            'IconActive'            => false,
                            'IconValue'             => 'info',
                            'Color'                 => 16711680,
                        ],
                        [
                            'Value'                 => 'Heat',
                            'Caption'               => 'Heat',
                            'IconActive'            => false,
                            'IconValue'             => 'info',
                            'Color'                 => 16711680,
                        ],
                        [
                            'Value'                 => 'ventilation',
                            'Caption'               => 'Ventilation',
                            'IconActive'            => false,
                            'IconValue'             => 'info',
                            'Color'                 => 16711680,
                        ],
                        [
                            'Value'                 => 'boost',
                            'Caption'               => 'Boost',
                            'IconActive'            => false,
                            'IconValue'             => 'info',
                            'Color'                 => 16711680,
                        ],
                    ],
                ],
                'receive' => 'enum:201'
            ],
            'anti_freeze' => [
                'name'         => 'Anti Freeze',
                'type'         => VARIABLETYPE_BOOLEAN,
                'action'       => true,
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                ],
                'receive' => 'boolean:200'
            ],
            'current_humidity' => [
                'name'         => 'Humidity',
                'type'         => VARIABLETYPE_FLOAT,
                'action'       => false,
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' %',
                    'ICON'         => 'Gauge',
                    'MIN'          => 0,
                    'MAX'          => 100,
                    'DIGITS'       => 2,
                ],
                'receive' => 'number:200'
            ],
            'target_humidity' => [
                'name'         => 'Target Humidity',
                'type'         => VARIABLETYPE_FLOAT,
                'action'       => true,
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' %',
                    'ICON'         => 'Gauge',
                    'MIN'          => 40,
                    'MAX'          => 75,
                    'DIGITS'       => 2,
                ],
                'receive' => 'number:202'
            ],
        ],

        'linkedgo-st1820-floor-thermostat' => [
            'enable' => [
                'name'         => 'State',
                'type'         => VARIABLETYPE_BOOLEAN,
                'action'       => true,
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                ],
                'receive' => 'boolean:202'
            ],
            'current_temperature' => [
                'name'         => 'Current Temperature',
                'action'       => false,
                'type'         => VARIABLETYPE_FLOAT,
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' °C',
                    'MIN'          => 0,
                    'MAX'          => 100,
                ],
                'receive' => 'number:201'
            ],
            'target_temperature' => [
                'name'         => 'Target Temperature',
                'action'       => true,
                'type'         => VARIABLETYPE_FLOAT,
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' °C',
                    'MIN'          => 5,
                    'MAX'          => 30,
                ],
                'receive' => 'number:202'
            ],
            'anti_freeze' => [
                'name'         => 'Anti Freeze',
                'type'         => VARIABLETYPE_BOOLEAN,
                'action'       => true,
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                ],
                'receive' => 'boolean:200'
            ],
            'current_humidity' => [
                'name'         => 'Humidity',
                'type'         => VARIABLETYPE_FLOAT,
                'action'       => false,
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' %',
                    'ICON'         => 'Gauge',
                    'MIN'          => 0,
                    'MAX'          => 100,
                    'DIGITS'       => 2,
                ],
                'receive' => 'number:200'
            ],
            'child_lock' => [
                'name'         => 'Child Lock',
                'type'         => VARIABLETYPE_BOOLEAN,
                'action'       => true,
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                ],
                'receive' => 'boolean:201'
            ],
        ],
        'simple-water-valve-controller' => [
            'position' => [
                'name'         => 'Position',
                'type'         => VARIABLETYPE_INTEGER,
                'action'       => true,
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_SLIDER,
                    'SUFFIX'       => ' %',
                    'USAGE_TYPE'   => 4
                ],
                'receive' => 'number:200'
            ],
            'has_Power' => [
                'name'         => 'Power supply',
                'type'         => VARIABLETYPE_BOOLEAN,
                'action'       => false,
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                ],
                'receive' => 'boolean:200'
            ],
            'temperature' => [
                'name'         => 'Current Temperature',
                'action'       => false,
                'type'         => VARIABLETYPE_FLOAT,
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' °C',
                    'MIN'          => 0,
                    'MAX'          => 100,
                ],
                'receive'           => 'temperature:100',
                'receivePayloadKey' => 'tC'

            ],
        ],
        'neo-water-valve-advanced' => [
            'state' => [
                'name'         => 'State',
                'type'         => VARIABLETYPE_BOOLEAN,
                'action'       => true,
                 'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'ICON'         => 'Valve',
                    'OPTIONS'      => [
                        [
                            "Value" => true,
                            "Caption" => "Open",
                            "IconActive" => false,
                            "Icon" => "",
                            "ColorActive" => true,
                            "ColorValue" => 65280
                        ],
                        [
                            "Value" => false,
                            "Caption" => "Closed",
                            "IconActive" => false,
                            "Icon" => "",
                            "ColorActive" => true,
                            "ColorValue" => 16711680
                        ]
                    ],
                ],

                'receive' => 'boolean:200'
            ],
            'flow_rate' => [
                'name'         => 'Flow rate',
                'type'         => VARIABLETYPE_FLOAT,
                'action'       => false,
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' m3/min',
                    'ICON'         => 'water',
                    'MIN'          => 0,
                    'MAX'          => 0.075,
                    'DIGITS'       => 2,
                    'STEP_SIZE'    => 0.1 
                ],
                'receive' => 'number:200'
            ],
                'water_pressure' => [
                'name'         => 'Water Pressure',
                'type'         => VARIABLETYPE_FLOAT,
                'action'       => false,
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' kPa',
                    'ICON'         => 'water',
                    'MIN'          => 0,
                    'MAX'          => 1350,
                    'STEP_SIZE'    => 1 
                ],
                'receive' => 'number:201'
            ],
                'water_temperature' => [
                'name'         => 'Water temperature',
                'type'         => VARIABLETYPE_FLOAT,
                'action'       => false,
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' °C',
                    'ICON'         => 'water',
                    'MIN'          => -25,
                    'MAX'          => 80,
                    'STEP_SIZE'    => 1 
                ],
                'receive' => 'number:202'
            ],
                'water_consumption' => [
                'name'         => 'Water consumption',
                'type'         => VARIABLETYPE_FLOAT,
                'action'       => false,
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                    'SUFFIX'       => ' m3',
                    'ICON'         => 'water'
                ],
                'receive' => 'object:200',
                'objectValue' => 'counter:total'
            ],

        ],
    ];
}
