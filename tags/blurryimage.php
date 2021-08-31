<?php

use Kirby\Cms\Html;
use Kirby\Cms\Url;
use Kirby\Toolkit\A;

return [
    'attr' => [
        'alt',
        'caption',
        'class',
        'height',
        'imgclass',
        'link',
        'linkclass',
        'rel',
        'target',
        'title',
        'width'
    ],
    'html' => function ($tag) {
        if ($tag->file = $tag->file($tag->value)) {
            $tag->src     = $tag->file->url();
            $tag->alt     = $tag->alt     ?? $tag->file->alt()->or(' ')->value();
            $tag->title   = $tag->title   ?? $tag->file->title()->value();
            $tag->caption = $tag->caption ?? $tag->file->caption()->value();
        } else {
            $tag->src = Url::to($tag->value);
        }

        $link = function ($img) use ($tag) {
            if (empty($tag->link) === true) {
                return $img;
            }

            if ($link = $tag->file($tag->link)) {
                $link = $link->url();
            } else {
                $link = $tag->link === 'self' ? $tag->src : $tag->link;
            }

            return Html::a($link, [$img], [
                'rel'    => $tag->rel,
                'class'  => $tag->linkclass,
                'target' => $tag->target
            ]);
        };

        $imageAttr = [
            'width'  => $tag->width,
            'height' => $tag->height,
            'class'  => $tag->imgclass,
            'title'  => $tag->title,
            'alt'    => $tag->alt ?? ' '
        ];

        if ($tag->file !== null) {
            $dataUri = $tag->file->placeholderUri();
            $preset = $tag->kirby()->option('kirby-extended.blurry-placeholder.kirbytag.srcset-preset');
            $sizes = $tag->kirby()->option('kirby-extended.blurry-placeholder.kirbytag.sizes', 'auto');
            $hasPreset = $preset !== null;

            $image = Html::img($dataUri, A::merge($imageAttr, [
                'data-src' => !$hasPreset ? $tag->src : null,
                'data-srcset' => $hasPreset ? $tag->file->srcset($preset) : null,
                'data-sizes' => $hasPreset ? $sizes : null,
                'data-lazyload' => 'true',
            ]));
        } else {
            $image = Html::img($tag->src, $imageAttr);
        }

        if ($tag->kirby()->option('kirbytext.image.figure', true) === false) {
            return $link($image);
        }

        // render KirbyText in caption
        if ($tag->caption) {
            $tag->caption = [$tag->kirby()->kirbytext($tag->caption, [], true)];
        }

        return Html::figure([$link($image)], $tag->caption, [
            'class' => $tag->class
        ]);
    }
];
