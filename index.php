<?php

use JohannSchopplich\BlurryPlaceholder;

load([
    'JohannSchopplich\\BlurryPlaceholder' => 'BlurryPlaceholder.php'
], __DIR__);

\Kirby\Cms\App::plugin('johannschopplich/blurry-placeholder', [
    'fileMethods' => [
        'placeholder' => function (float|null $ratio = null) {
            return BlurryPlaceholder::image($this, $ratio);
        },
        'placeholderUri' => function (float|null $ratio = null) {
            return BlurryPlaceholder::uri($this, $ratio);
        }
    ],
    'tags' => [
        'blurryimage' => require __DIR__ . '/tags/blurryimage.php'
    ]
]);
