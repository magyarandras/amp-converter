<?php
namespace magyarandras\AMPConverter\TagConverter;

use magyarandras\AMPConverter\TagConverterInterface;

class AMPIframe implements TagConverterInterface{

    private $necessary_scripts = [];

    private $extension_script = '<script async custom-element="amp-iframe" src="https://cdn.ampproject.org/v0/amp-iframe-0.1.js"></script>';

    public function convert(\DOMDocument $doc){
        $query = '//iframe';

        $xpath = new \DOMXPath($doc);

        $entries = $xpath->query($query);

        if($entries->length > 0){
            $this->necessary_scripts[] = $this->extension_script;
        }
        
        foreach ($entries as $tag) {

            if($tag->hasAttribute('width') && $tag->hasAttribute('height')){                
                    $width = $tag->getAttribute('width');
                    $height = $tag->getAttribute('height');
            }
            else{
                $width = '600';
                $height = '450';
            }

            if($tag->getAttribute('sandbox')){
                $sandbox = $tag->getAttribute('sandbox');
            }
            else{
                $sandbox = 'allow-scripts allow-same-origin allow-popups';
            }

            $src = $tag->getAttribute('src');

            $ampiframe = $doc->createElement('amp-iframe');

            $ampiframe->setAttribute('src', $src);
            $ampiframe->setAttribute('sandbox', $sandbox);
            $ampiframe->setAttribute('width', $width);
            $ampiframe->setAttribute('height', $height);

            if($tag->hasAttribute('allowfullscreen')){
                $ampiframe->setAttribute('allowfullscreen', '');
            }

            $ampiframe->setAttribute('layout', 'responsive');

            $tag->parentNode->replaceChild($ampiframe, $tag);
            
        }

        return $doc;
    }

    public function getNecessaryScripts(){
        return $this->necessary_scripts;
    }



}