<?php

namespace magyarandras\AMPConverter\TagConverter;

use magyarandras\AMPConverter\TagConverterInterface;

class AMPGist implements TagConverterInterface{

    private $necessary_scripts = [];

    private $extension_script = '<script async custom-element="amp-gist" src="https://cdn.ampproject.org/v0/amp-gist-0.1.js"></script>';

    public function convert(\DOMDocument $doc){

        // /https?\:\/\/gist.github.com\/.*\/([0-9a-z]+)\.js/i
        $query = '//script[php:functionString(\'preg_match\', \'/https?\:\/\/gist.github.com\/.*\/([0-9a-z]+)\.js/i\', @src) > 0]';

        $xpath = new \DOMXPath($doc);

        $xpath->registerNamespace("php", "http://php.net/xpath");
        $xpath->registerPhpFunctions('preg_match');

        $entries = $xpath->query($query);

        if($entries->length > 0){
            $this->necessary_scripts[] = $this->extension_script;
        }

        foreach($entries as $tag){
            preg_match('/https?\:\/\/gist.github.com\/.*\/([0-9a-z]+)\.js/i', $tag->getAttribute('src'), $id_match);
            $gist_id = $id_match[1];

            $gist = $doc->createElement('amp-gist');

            $gist->setAttribute('data-gistid', $gist_id);
            $gist->setAttribute('layout', 'fixed-height');
            $gist->setAttribute('height', '250');

            $tag->parentNode->replaceChild($gist, $tag);

            
        }

        return $doc;

    }

    public function getNecessaryScripts(){
        return $this->necessary_scripts;
    }

    

}