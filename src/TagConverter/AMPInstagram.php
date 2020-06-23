<?php

namespace magyarandras\AMPConverter\TagConverter;

use magyarandras\AMPConverter\TagConverterInterface;

class AMPInstagram implements TagConverterInterface
{
    private $necessary_scripts = [];

    private $extension_script = '<script async custom-element="amp-instagram" src="https://cdn.ampproject.org/v0/amp-instagram-0.1.js"></script>';

    public function convert(\DOMDocument $doc)
    {

        // /instagram.com\/p\/([0-9a-zA-Z_\-]+)/i
        $query = '//blockquote[@class="instagram-media" and php:functionString(\'preg_match\', \'/instagram.com\/p\/([0-9a-zA-Z_\-]+)/i\', @data-instgrm-permalink) > 0]';

        $xpath = new \DOMXPath($doc);

        $xpath->registerNamespace("php", "http://php.net/xpath");
        $xpath->registerPhpFunctions('preg_match');

        $entries = $xpath->query($query);

        if ($entries->length > 0) {
            $this->necessary_scripts[] = $this->extension_script;
        }

        foreach ($entries as $tag) {
            preg_match('/instagram.com\/p\/([0-9a-zA-Z_\-]+)/i', $tag->getAttribute('data-instgrm-permalink'), $id_match);
            $post_id = $id_match[1];

            $ampinstagram = $doc->createElement('amp-instagram');

            $ampinstagram->setAttribute('data-shortcode', $post_id);
            $ampinstagram->setAttribute('data-captioned', '');
            $ampinstagram->setAttribute('width', '400');
            $ampinstagram->setAttribute('height', '400');
            $ampinstagram->setAttribute('layout', 'responsive');

            $tag->parentNode->replaceChild($ampinstagram, $tag);
        }

        return $doc;
    }

    public function getNecessaryScripts()
    {
        return $this->necessary_scripts;
    }
}
