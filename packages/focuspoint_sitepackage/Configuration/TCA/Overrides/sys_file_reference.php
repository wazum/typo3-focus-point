<?php

declare(strict_types=1);

$GLOBALS['TCA']['tt_content']['types']['textmedia']['columnsOverrides']['assets']['config']['overrideChildTca']['columns']['crop']['config'] = [
    'cropVariants' => [
        'default' => [
            'title' => 'Desktop',
            'allowedAspectRatios' => [
                '16:9' => [
                    'title' => '16:9',
                    'value' => 16 / 9,
                ],
                '4:3' => [
                    'title' => '4:3',
                    'value' => 4 / 3,
                ],
                '1:1' => [
                    'title' => '1:1',
                    'value' => 1.0,
                ],
                'free' => [
                    'title' => 'Free',
                    'value' => 0.0,
                ],
            ],
            'selectedRatio' => 'free',
            'focusArea' => [
                'x' => 1 / 3,
                'y' => 1 / 3,
                'width' => 1 / 3,
                'height' => 1 / 3,
            ],
        ],
        'mobile' => [
            'title' => 'Mobile',
            'allowedAspectRatios' => [
                '9:16' => [
                    'title' => '9:16',
                    'value' => 9 / 16,
                ],
                '3:4' => [
                    'title' => '3:4',
                    'value' => 3 / 4,
                ],
                '1:1' => [
                    'title' => '1:1',
                    'value' => 1.0,
                ],
                'free' => [
                    'title' => 'Free',
                    'value' => 0.0,
                ],
            ],
            'selectedRatio' => 'free',
            'focusArea' => [
                'x' => 1 / 3,
                'y' => 1 / 3,
                'width' => 1 / 3,
                'height' => 1 / 3,
            ],
        ],
    ],
];
