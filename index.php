<?php

load([
    'KirbyExtended\\BlurryPlaceholder' => 'classes/KirbyExtended/BlurryPlaceholder.php'
], __DIR__);

\Kirby\Cms\App::plugin('kirby-extended/blurry-placeholder', [
    'fileMethods' => [
        'placeholder' => function (?float $ratio = null) {
            return \KirbyExtended\BlurryPlaceholder::image($this, $ratio);
        },
        'placeholderUri' => function (?float $ratio = null) {
            return \KirbyExtended\BlurryPlaceholder::uri($this, $ratio);
        }
    ],
    'tags' => [
        'blurryimage' => require __DIR__ . '/tags/blurryimage.php'
    ]
]);
