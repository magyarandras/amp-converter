<?php
namespace magyarandras\AMPConverter\Util;

class ImageSize
{
    public static function getImageSize($base_url, $url, $timeout = 10)
    {
        $fasterImageSize = new \FasterImage\FasterImage();
        $fasterImageSize->setTimeout($timeout);

        if (preg_match('/https?:\/\//i', $url)) {
            $img_url = $url;
        } else {
            if (substr($base_url, -1) == '/') {
                $base_url = substr($base_url, 0, -1);
            }

            if (substr($url, 0, 1) == '/') {
                $url = substr($url, 1);
            }

            $img_url = $base_url . '/' . $url;
        }

        $imageSize = $fasterImageSize->batch([
            $img_url
        ]);

        if ($imageSize[$img_url]['size'] == 'failed') {
            return null;
        }

        list($width, $height) = $imageSize[$img_url]['size'];
        return [
            'width' => $width,
            'height' => $height,
            'type' => $imageSize[$img_url]['type']
        ];
        
    }
}
