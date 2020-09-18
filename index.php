<?php

@include_once __DIR__ . '/vendor/autoload.php';

use Kirby\Cms\App as Kirby;
use KirbyExtended\BlurryPlaceholder;

Kirby::plugin('kirby-extended/blurry-placeholder', [
    'options' => [
        'enable' => true,
        'pixel-target' => 60
    ],
    'fileMethods' => [
        'placeholder' => fn() => BlurryPlaceholder::image($this),
        'placeholderUri' => fn() => BlurryPlaceholder::uri($this)
    ],
    'tags' => [
        'blurryimage' => require __DIR__ . '/tags/blurryimage.php'
    ]
]);
