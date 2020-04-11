<?php
namespace magyarandras\AMPConverter\TagConverter;

use magyarandras\AMPConverter\TagConverterInterface;

class AMPYoutube implements TagConverterInterface
{
    private $necessary_scripts = [];

    private $extension_script = '<script async custom-element="amp-youtube" src="https://cdn.ampproject.org/v0/amp-youtube-0.1.js"></script>';

    public function convert(\DOMDocument $doc)
    {
        $query = '//iframe[php:functionString(\'preg_match\', \'/youtube\.com\/(?:v|embed)\/([a-zA-z0-9_-]+)/i\', @src) > 0]';

        $xpath = new \DOMXPath($doc);

        $xpath->registerNamespace("php", "http://php.net/xpath");
        $xpath->registerPhpFunctions('preg_match');

        $entries = $xpath->query($query);

        if ($entries->length > 0) {
            $this->necessary_scripts[] = $this->extension_script;
        }

        foreach ($entries as $tag) {
            preg_match('/youtube\.com\/(?:v|embed)\/([a-zA-z0-9_-]+)/i', $tag->getAttribute('src'), $id_match);
            $video_id = $id_match[1];

            $youtube_video = $doc->createElement('amp-youtube');

            $youtube_video->setAttribute('data-videoid', $video_id);
            $youtube_video->setAttribute('width', '480');
            $youtube_video->setAttribute('height', '270');
            $youtube_video->setAttribute('layout', 'responsive');

            $tag->parentNode->replaceChild($youtube_video, $tag);
        }

        return $doc;
    }

    public function getNecessaryScripts()
    {
        return $this->necessary_scripts;
    }
}
