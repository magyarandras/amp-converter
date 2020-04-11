<?php
namespace magyarandras\AMPConverter\TagConverter;

use magyarandras\AMPConverter\TagConverterInterface;

class AMPVideo implements TagConverterInterface
{
    private $necessary_scripts = [];

    private $extension_script = '<script async custom-element="amp-video" src="https://cdn.ampproject.org/v0/amp-video-0.1.js"></script>';

    public function convert(\DOMDocument $doc)
    {
        $query = '//video';

        $xpath = new \DOMXPath($doc);

        $entries = $xpath->query($query);

        if ($entries->length > 0) {
            $this->necessary_scripts[] = $this->extension_script;
        }
        
        $allowed_attributes = ['src', 'poster', 'autoplay', 'controls', 'loop', 'crossorigin'];

        foreach ($entries as $tag) {
            if ($tag->hasAttribute('width') && $tag->hasAttribute('height')) {
                $width = $tag->getAttribute('width');
                $height = $tag->getAttribute('height');


                $video = $doc->createElement('amp-video');

                $video->setAttribute('width', $width);
                $video->setAttribute('height', $height);
                $video->setAttribute('layout', 'responsive');


                foreach ($allowed_attributes as $attribute) {
                    if ($tag->hasAttribute($attribute)) {
                        $video->setAttribute($attribute, $tag->getAttribute($attribute));
                    }
                }

                if ($tag->hasChildNodes()) {
                    foreach ($tag->childNodes as $node) {
                        $video->appendChild($node->cloneNode(true));
                    }
                }
                

                $tag->parentNode->replaceChild($video, $tag);
            } else {
                $tag->parentNode->removeChild($tag);
            }
        }

        return $doc;
    }

    public function getNecessaryScripts()
    {
        return $this->necessary_scripts;
    }
}
