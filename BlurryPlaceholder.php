<?php

namespace JohannSchopplich;

class BlurryPlaceholder
{
    /**
     * Returns the blurry image placeholder as SVG for the given file
     */
    public static function image(\Kirby\Filesystem\Asset|\Kirby\Cms\File $file, array $options = []): string
    {
        $kirby = kirby();
        $options['pixelTarget'] ??= $kirby->option('johannschopplich.blurry-placeholder.pixel-target', 60);
        $options['ratio'] ??= $file->ratio();

        // Aims for an image of ~P pixels (w * h = ~P)
        $height = sqrt($options['pixelTarget'] / $options['ratio']);
        $width = $options['pixelTarget'] / $height;

        $thumbOptions = [
            'width'   => round($width),
            'height'  => round($height),
            'crop'    => true,
            'quality' => 40
        ];

        if ($format = $kirby->option('thumbs.format')) {
            $thumbOptions['format'] = $format;
        }

        $thumb = $file->thumb($thumbOptions);

        $svgHeight = number_format($height, 2, '.', '');
        $svgWidth = number_format($width, 2, '.', '');
        $svgUri = $thumb->dataUri();

        $options['transparent'] ??= static::hasAlphaChannel($thumb);
        $alphaFilter = '';

        // If the image doesn't include an alpha channel itself, apply an additional filter
        // to remove the alpha channel from the blur at the edges
        if (!$options['transparent']) {
            $alphaFilter = <<<EOD
                <feComponentTransfer>
                    <feFuncA type="discrete" tableValues="1 1"></feFuncA>
                </feComponentTransfer>
                EOD;
        }

        // Wrap the blurred image in a SVG to avoid rasterizing the filter
        $svg = <<<EOD
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 {$svgWidth} {$svgHeight}">
              <filter id="b" color-interpolation-filters="sRGB">
                <feGaussianBlur stdDeviation=".5"></feGaussianBlur>
                {$alphaFilter}
              </filter>
              <image filter="url(#b)" x="0" y="0" width="100%" height="100%" href="{$svgUri}"></image>
            </svg>
            EOD;

        return $svg;
    }

    /**
     * Returns the blurry image placeholder as data URI scheme
     */
    public static function uri(\Kirby\Filesystem\Asset|\Kirby\Cms\File $file, array $options = []): string
    {
        $svg = static::image($file, $options);
        return 'data:image/svg+xml;charset=utf-8,' . static::encodeSvg($svg);
    }

    /**
     * Checks whether a generated thumbnail contains an alpha channel
     */
    private static function hasAlphaChannel(\Kirby\Cms\FileVersion $file): bool
    {
        // Create a GD image from the thumbnail
        $image = imagecreatefromstring($file->read());
        $width = imagesx($image);
        $height = imagesy($image);

        for ($i = 0; $i < $width; $i++) {
            for ($j = 0; $j < $height; $j++) {
                if (imagecolorat($image, $i, $j) & 0x7F000000) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Returns the URL-encoded string of the given SVG
     */
    private static function encodeSvg(string $data): string
    {
        // Optimizes the data URI length by deleting line breaks and
        // removing unnecessary spaces
        $data = preg_replace('/\s+/', ' ', $data);
        $data = preg_replace('/> </', '><', $data);

        $data = rawurlencode($data);

        // Back-decode certain characters to improve compression
        // except `%20` to be compliant with W3C guidelines
        $data = str_replace(
            ['%2F', '%3A', '%3D'],
            ['/', ':', '='],
            $data
        );

        return $data;
    }
}
