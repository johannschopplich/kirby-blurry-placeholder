<?php

namespace KirbyExtended;

class BlurryPlaceholder
{
    /**
     * Creates a blurry image placeholder
     *
     * @param \Kirby\Cms\File $file
     * @param float|null $ratio
     * @return string
     * @throws \Kirby\Exception\InvalidArgumentException
     */
    public static function image(\Kirby\Cms\File $file, $ratio = null): string
    {
        $pixelTarget = option('kirby-extended.blurry-placeholder.pixel-target', 60);

        // Aims for an image of ~P pixels (w * h = ~P)
        $placeholderHeight = sqrt($pixelTarget / ($ratio ?? $file->ratio()));
        $placeholderWidth = $pixelTarget / $placeholderHeight;

        $placeholderImage = $file->thumb([
            'width'   => round($placeholderWidth),
            'height'  => round($placeholderHeight),
            'crop'    => true,
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
     * @param float|null $ratio
     * @return string
     * @throws \Kirby\Exception\InvalidArgumentException
     */
    public static function uri(\Kirby\Cms\File $file, $ratio = null): string
    {
        $svg = static::image($file, $ratio);
        $dataUri = 'data:image/svg+xml;charset=utf-8,' . static::svgToUri($svg);

        return $dataUri;
    }

    /**
     * Returns the URI-encoded string of an SVG
     *
     * @param string $data
     * @return string
     */
    private static function svgToUri(string $data): string
    {
        // Optimizes the data URI length by deleting line breaks and
        // removing unnecessary spaces
        $data = preg_replace('/\s+/', ' ', $data);
        $data = preg_replace('/> </', '><', $data);

        $data = rawurlencode($data);

        // Back-decode certain characters to improve compression
        // except '%20' to be compliant with W3C guidelines
        $data = str_replace(
            ['%2F', '%3A', '%3D'],
            ['/', ':', '='],
            $data
        );

        return $data;
    }
}
