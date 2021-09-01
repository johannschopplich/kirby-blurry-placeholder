<?php

load([
    'KirbyExtended\\BlurryPlaceholder' => 'classes/KirbyExtended/BlurryPlaceholder.php'
], __DIR__);

\Kirby\Cms\App::plugin('kirby-extended/blurry-placeholder', [
    'fileMethods' => [
        'placeholder' => function () {
            return \KirbyExtended\BlurryPlaceholder::image($this);
        },
        'placeholderUri' => function () {
            return \KirbyExtended\BlurryPlaceholder::uri($this);
        }
    ],
    'tags' => [
        'blurryimage' => require __DIR__ . '/tags/blurryimage.php'
    ]
]);
