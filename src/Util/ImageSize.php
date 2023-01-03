<?php
namespace magyarandras\AMPConverter\Util;

class ImageSize
{
    public static function getImageSize($base_url, $url)
    {
        $FasterImageSize = new \FasterImage\FasterImage();

        if (preg_match('/https?:\/\//i', $url)) {
            $img_url = $url;
        } else {
            if (substr($base_url, -1) == '/') {
                $base_url = substr($base_url, 0, -1);
            }

            if (substr($url, 0, 1) != '/') {
                $url = '/' . $url;
            }

            $img_url = $base_url . '/' . $url;
        }

        $imageSize = $FasterImageSize->getImageSize($img_url);
             
        return $imageSize;
    }
}
