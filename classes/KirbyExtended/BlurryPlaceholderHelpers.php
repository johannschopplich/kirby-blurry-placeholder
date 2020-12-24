<?php

namespace KirbyExtended;

class BlurryPlaceholderHelpers
{
    /**
     * Returns the URI-encoded string of an SVG
     *
     * @param string $data
     * @return string
     */
    public static function svgToUri(string $data): string
    {
        // Optimizes the data URI length by deleting line breaks and
        // removing unnecessary spaces
        $data = preg_replace('/\s+/', ' ', $data);
        $data = preg_replace('/> </', '><', $data);

        $data = rawurlencode($data);

        // Back-decode certain characters to improve compression
        $search = ['%20', '%2F', '%3A', '%3D'];
        $replace = [' ', '/', ':', '='];
        $data = str_replace($search, $replace, $data);

        return $data;
    }
}
