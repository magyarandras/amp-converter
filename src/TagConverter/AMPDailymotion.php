<?php
namespace magyarandras\AMPConverter\TagConverter;

use magyarandras\AMPConverter\TagConverterInterface;

class AMPDailymotion implements TagConverterInterface{

    private $necessary_scripts = [];

    private $extension_script = '<script async custom-element="amp-dailymotion" src="https://cdn.ampproject.org/v0/amp-dailymotion-0.1.js"></script>';

    public function convert(\DOMDocument $doc){

        
        $query = '//iframe[php:functionString(\'preg_match\', \'/dailymotion.com\/embed\/video\/([0-9a-zA-Z]+)/i\', @src) > 0]';

        $xpath = new \DOMXPath($doc);

        $xpath->registerNamespace("php", "http://php.net/xpath");
        $xpath->registerPhpFunctions('preg_match');

        $entries = $xpath->query($query);

        if($entries->length > 0){
            $this->necessary_scripts[] = $this->extension_script;
        }

        foreach($entries as $tag){
            preg_match('/dailymotion.com\/embed\/video\/([0-9a-zA-Z]+)/i', $tag->getAttribute('src'), $id_match);
            $video_id = $id_match[1];

            $dailymotion = $doc->createElement('amp-dailymotion');

            $dailymotion->setAttribute('data-videoid', $video_id);
            $dailymotion->setAttribute('width', '480');
            $dailymotion->setAttribute('height', '270');
            $dailymotion->setAttribute('layout', 'responsive');

            $tag->parentNode->replaceChild($dailymotion, $tag);

            
        }

        return $doc;

    }

    public function getNecessaryScripts(){
        return $this->necessary_scripts;
    }

    

}