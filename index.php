<?php

@include_once __DIR__ . '/vendor/autoload.php';

use Kirby\Cms\App as Kirby;
use KirbyExtended\BlurryPlaceholder;

Kirby::plugin('kirby-extended/blurry-placeholder', [
    'options' => [
        'pixel-target' => 60,
        'srcset' => [
            'enable' => false,
            'preset' => null,
            'sizes' => 'auto'
        ]
    ],
    'fileMethods' => [
        'placeholder' => function () {
            return BlurryPlaceholder::image($this);
        },
        'placeholderUri' => function () {
            return BlurryPlaceholder::uri($this);
        }
    ],
    'tags' => [
        'blurryimage' => require __DIR__ . '/tags/blurryimage.php'
    ]
]);
