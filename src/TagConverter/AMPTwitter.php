<?php

namespace magyarandras\AMPConverter\TagConverter;

use magyarandras\AMPConverter\TagConverterInterface;

class AMPTwitter implements TagConverterInterface
{
    private $necessary_scripts = [];

    private $extension_script = '<script async custom-element="amp-twitter" src="https://cdn.ampproject.org/v0/amp-twitter-0.1.js"></script>';

    public function convert(\DOMDocument $doc)
    {

        // /https?\:\/\/twitter.com\/.*\/status\/([0-9a-z]+)/i
        $query = '//blockquote[@class="twitter-tweet"]/a[php:functionString(\'preg_match\', \'/https?\:\/\/twitter.com\/.*\/status\/([0-9a-z]+)/i\', @href) > 0]';

        $xpath = new \DOMXPath($doc);

        $xpath->registerNamespace("php", "http://php.net/xpath");
        $xpath->registerPhpFunctions('preg_match');

        $entries = $xpath->query($query);

        if ($entries->length > 0) {
            $this->necessary_scripts[] = $this->extension_script;
        }

        foreach ($entries as $tag) {
            preg_match('/https?\:\/\/twitter.com\/.*\/status\/([0-9a-z]+)/i', $tag->getAttribute('href'), $id_match);
            $post_id = $id_match[1];

            $amptwitter = $doc->createElement('amp-twitter');


            $amptwitter->setAttribute('width', '375');
            $amptwitter->setAttribute('height', '472');
            $amptwitter->setAttribute('layout', 'responsive');
            $amptwitter->setAttribute('data-tweetid', $post_id);

            $tag->parentNode->parentNode->replaceChild($amptwitter, $tag->parentNode);
        }

        return $doc;
    }

    public function getNecessaryScripts()
    {
        return $this->necessary_scripts;
    }
}
