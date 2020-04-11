<?php
namespace magyarandras\AMPConverter\TagConverter;

use magyarandras\AMPConverter\TagConverterInterface;

class AMPVimeo implements TagConverterInterface
{
    private $necessary_scripts = [];

    private $extension_script = '<script async custom-element="amp-vimeo" src="https://cdn.ampproject.org/v0/amp-vimeo-0.1.js"></script>';

    public function convert(\DOMDocument $doc)
    {
        $query = '//iframe[php:functionString(\'preg_match\', \'/https?:\/\/player.vimeo.com\/video\/([0-9]+)/i\', @src) > 0]';

        $xpath = new \DOMXPath($doc);

        $xpath->registerNamespace("php", "http://php.net/xpath");
        $xpath->registerPhpFunctions('preg_match');

        $entries = $xpath->query($query);

        if ($entries->length > 0) {
            $this->necessary_scripts[] = $this->extension_script;
        }

        foreach ($entries as $tag) {
            preg_match('/https?:\/\/player.vimeo.com\/video\/([0-9]+)/i', $tag->getAttribute('src'), $id_match);
            $video_id = $id_match[1];

            $vimeo = $doc->createElement('amp-vimeo');

            $vimeo->setAttribute('data-videoid', $video_id);
            $vimeo->setAttribute('width', '500');
            $vimeo->setAttribute('height', '281');
            $vimeo->setAttribute('layout', 'responsive');

            $tag->parentNode->replaceChild($vimeo, $tag);
        }

        return $doc;
    }

    public function getNecessaryScripts()
    {
        return $this->necessary_scripts;
    }
}
