<?php

namespace KirbyExtended;

use Kirby\Cms\File;
use Kirby\Exception\InvalidArgumentException;

class BlurryPlaceholder
{
    /**
     * Creates a blurry image placeholder
     *
     * @param \Kirby\Cms\File $file
     * @return string
     * @throws InvalidArgumentException
     */
    public static function image(File $file): string
    {
        $pixelTarget = option('kirby-extended.blurry-placeholder.pixel-target', 60);

        // Aims for an image of ~P pixels (w * h = ~P)
        $placeholderHeight = sqrt($pixelTarget / $file->ratio());
        $placeholderWidth = $pixelTarget / $placeholderHeight;

        $placeholderImage = $file->thumb([
            'width'   => round($placeholderWidth),
            'height'  => round($placeholderHeight),
            'quality' => 60
        ])->dataUri();

        $svgHeight = number_format($placeholderHeight, 2, '.', '');
        $svgWidth = number_format($placeholderWidth, 2, '.', '');

        // Wrap the blurred image in a SVG to avoid rasterizing the filter
        $svg = <<<EOD
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 {$svgWidth} {$svgHeight}">
              <filter id="b" color-interpolation-filters="sRGB">
                <feGaussianBlur stdDeviation=".5"></feGaussianBlur>
                <feComponentTransfer>
                  <feFuncA type="discrete" tableValues="1 1"></feFuncA>
                </feComponentTransfer>
              </filter>
              <image filter="url(#b)" x="0" y="0" width="100%" height="100%" href="{$placeholderImage}"></image>
            </svg>
            EOD;

        return $svg;
    }

    /**
     * Returns the blurry image placeholder as data URI scheme
     *
     * @param \Kirby\Cms\File $file
     * @return string
     * @throws InvalidArgumentException
     */
    public static function uri(File $file): string
    {
        $svg = self::image($file);
        $dataUri = 'data:image/svg+xml;charset=utf-8,' . BlurryPlaceholderHelpers::svgToUri($svg);

        return $dataUri;
    }
}
