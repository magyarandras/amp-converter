<?php
namespace magyarandras\AMPConverter\TagConverter;

use magyarandras\AMPConverter\TagConverterInterface;

class AMPAudio implements TagConverterInterface{

    private $necessary_scripts = [];

    private $extension_script = '<script async custom-element="amp-audio" src="https://cdn.ampproject.org/v0/amp-audio-0.1.js"></script>';

    public function convert(\DOMDocument $doc){
        $query = '//audio';

        $xpath = new \DOMXPath($doc);

        $entries = $xpath->query($query);

        if($entries->length > 0){
            $this->necessary_scripts[] = $this->extension_script;
        }
        
        $allowed_attributes = ['src', 'autoplay', 'controls', 'loop', 'crossorigin'];

        foreach ($entries as $tag) {


                $audio = $doc->createElement('amp-audio');


                foreach($allowed_attributes as $attribute){
                    if($tag->hasAttribute($attribute)){
                        $audio->setAttribute($attribute, $tag->getAttribute($attribute));
                    }
                }

                if($tag->hasChildNodes()){
                    foreach($tag->childNodes as $node){
                        $audio->appendChild($node->cloneNode(true));
                        
                    }
                }   
                

                $tag->parentNode->replaceChild($audio, $tag);
            
        }

        return $doc;
    }

    public function getNecessaryScripts(){
        return $this->necessary_scripts;
    }



}