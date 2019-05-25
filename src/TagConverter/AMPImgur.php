<?php
namespace magyarandras\AMPConverter\TagConverter;

use magyarandras\AMPConverter\TagConverterInterface;

class AMPImgur implements TagConverterInterface{

    private $necessary_scripts = [];

    private $extension_script = '<script async custom-element="amp-imgur" src="https://cdn.ampproject.org/v0/amp-imgur-0.1.js"></script>';

    public function convert(\DOMDocument $doc){
        //<blockquote class="imgur-embed-pub" lang="en" data-id="XVMu7rB"><a href="//imgur.com/XVMu7rB">can&#39;t see? no problem</a></blockquote><script async src="//s.imgur.com/min/embed.js" charset="utf-8"></script>
        $query = '//blockquote[@class="imgur-embed-pub" and @data-id]';

        $xpath = new \DOMXPath($doc);

        $entries = $xpath->query($query);

        if($entries->length > 0){
            $this->necessary_scripts[] = $this->extension_script;
        }

        foreach ($entries as $tag) {

                if($tag->hasAttribute('data-id')){

                    $imgur = $doc->createElement('amp-imgur');

                    $imgur->setAttribute('data-imgur-id', $tag->getAttribute('data-id'));
                    
                    $imgur->setAttribute('width', '540');
                    $imgur->setAttribute('height', '670');
                    $imgur->setAttribute('layout', 'responsive');


                    $tag->parentNode->replaceChild($imgur, $tag);
                }
                else{
                    $tag->parentNode->removeChild($tag);
                }
                  
        }

        return $doc;
    }

    public function getNecessaryScripts(){
        return $this->necessary_scripts;
    }
}