<?php

use JohannSchopplich\BlurryPlaceholder;

load([
    'JohannSchopplich\\BlurryPlaceholder' => 'BlurryPlaceholder.php'
], __DIR__);

\Kirby\Cms\App::plugin('johannschopplich/blurry-placeholder', [
    'fileMethods' => [
        'placeholder' => function (float|null $ratio = null, bool|null $transparent = null) {
            return BlurryPlaceholder::image($this, $ratio, $transparent);
        },
        'placeholderUri' => function (float|null $ratio = null, bool|null $transparent = null) {
            return BlurryPlaceholder::uri($this, $ratio, $transparent);
        }
    ],
    'tags' => [
        'blurryimage' => require __DIR__ . '/tags/blurryimage.php'
    ]
]);
