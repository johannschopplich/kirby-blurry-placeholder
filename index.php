<?php

use JohannSchopplich\BlurryPlaceholder;

load([
    'JohannSchopplich\\BlurryPlaceholder' => 'BlurryPlaceholder.php'
], __DIR__);

\Kirby\Cms\App::plugin('johannschopplich/blurry-placeholder', [
    'fileMethods' => [
        /** @kql-allowed */
        'placeholder' => fn (array $options = []) => BlurryPlaceholder::image($this, $options),
        /** @kql-allowed */
        'placeholderUri' => fn (array $options = []) => BlurryPlaceholder::uri($this, $options)
    ],
    'tags' => [
        'blurryimage' => require __DIR__ . '/tags/blurryimage.php'
    ]
]);
