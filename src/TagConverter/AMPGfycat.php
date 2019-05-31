<?php
namespace magyarandras\AMPConverter\TagConverter;

use magyarandras\AMPConverter\TagConverterInterface;

class AMPGfycat implements TagConverterInterface{

    private $necessary_scripts = [];

    private $extension_script = '<script async custom-element="amp-gfycat" src="https://cdn.ampproject.org/v0/amp-gfycat-0.1.js"></script>';

    public function convert(\DOMDocument $doc){

        
        $query = '//iframe[php:functionString(\'preg_match\', \'/gfycat.com\/ifr\/([0-9a-zA-Z]+)/i\', @src) > 0]';

        $xpath = new \DOMXPath($doc);

        $xpath->registerNamespace("php", "http://php.net/xpath");
        $xpath->registerPhpFunctions('preg_match');

        $entries = $xpath->query($query);

        if($entries->length > 0){
            $this->necessary_scripts[] = $this->extension_script;
        }

        foreach($entries as $tag){
            preg_match('/gfycat.com\/ifr\/([0-9a-zA-Z]+)/i', $tag->getAttribute('src'), $id_match);
            $gfy_id = $id_match[1];

            $gfycat = $doc->createElement('amp-gfycat');

            $gfycat->setAttribute('data-gfyid', $gfy_id);
            $gfycat->setAttribute('width', '640');
            $gfycat->setAttribute('height', '360');
            $gfycat->setAttribute('layout', 'responsive');

            $tag->parentNode->replaceChild($gfycat, $tag);

            
        }

        return $doc;

    }

    public function getNecessaryScripts(){
        return $this->necessary_scripts;
    }

    

}