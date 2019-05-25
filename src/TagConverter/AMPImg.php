<?php
namespace magyarandras\AMPConverter\TagConverter;

use magyarandras\AMPConverter\TagConverterInterface;
use magyarandras\AMPConverter\Util\ImageSize;

class AMPImg implements TagConverterInterface{

    private $necessary_scripts = [
        
    ];

    //Base URL of the images
    private $base_url;

    public function __construct($base_url = ''){
        $this->base_url = $base_url;
    }

    public function convert(\DOMDocument $doc){

        $FastImageSize = new \FastImageSize\FastImageSize();

        $query = '//img';

        $xpath = new \DOMXPath($doc);

        $entries = $xpath->query($query);

        $allowed_attributes = ['src', 'width', 'height', 'alt', 'srcset', 'sizes'];
        
        foreach ($entries as $tag) {

            if($tag->hasAttribute('src')){

                $img = $doc->createElement('amp-img');

                if(!$tag->hasAttribute('width') || !$tag->hasAttribute('height')){
                    $imageSize = ImageSize::getImageSize($this->base_url, $tag->getAttribute('src'));
                    
                    if($imageSize){
                        $img->setAttribute('width', $imageSize['width']);
                        $img->setAttribute('height', $imageSize['height']);
                    }
                    else{
                        $tag->parentNode->removeChild($tag);
                        continue;
                    }
                    
                }

                foreach($allowed_attributes as $attribute){
                    if($tag->hasAttribute($attribute)){
                        $img->setAttribute($attribute, $tag->getAttribute($attribute));
                    }
                }

                $img->setAttribute('layout', 'responsive');

                $tag->parentNode->replaceChild($img, $tag);

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