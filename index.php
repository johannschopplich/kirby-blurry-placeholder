<?php

load([
    'KirbyExtended\\BlurryPlaceholder' => 'classes/KirbyExtended/BlurryPlaceholder.php'
], __DIR__);

\Kirby\Cms\App::plugin('kirby-extended/blurry-placeholder', [
    'fileMethods' => [
        'placeholder' => fn (?float $ratio = null) => \KirbyExtended\BlurryPlaceholder::image($this, $ratio),
        'placeholderUri' => fn (?float $ratio = null) => \KirbyExtended\BlurryPlaceholder::uri($this, $ratio)
    ],
    'tags' => [
        'blurryimage' => require __DIR__ . '/tags/blurryimage.php'
    ]
]);
