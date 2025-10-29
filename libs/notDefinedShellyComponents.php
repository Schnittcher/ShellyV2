<?php

declare(strict_types=1);
trait notDefinedComponents
{
    public static $notDefinedComponents = [
        '' => [
            'number:200' => [
                'type'         => VARIABLETYPE_INTEGER,
                'name'         => 'Postion',
                'ident'        => 'number_200',
                'presentation' => [
                    'PRESENTATION' => VARIABLE_PRESENTATION_SLIDER,
                    'SUFFIX'       => ' %',
                    'USAGE_TYPE'   => 3,
                    'STEP_SIZE'    => 10,
                ],
                'action'        => [
                    'method' => 'number.set',
                    'params' => ['id' => '200', 'value' => ''
                    ]
                ]
            ],

        ]
    ];
}