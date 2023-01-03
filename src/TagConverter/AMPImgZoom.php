<?php
namespace magyarandras\AMPConverter\TagConverter;

use magyarandras\AMPConverter\TagConverterInterface;
use magyarandras\AMPConverter\Util\ImageSize;

class AMPImgZoom implements TagConverterInterface
{
    private $necessary_scripts = [];

    private $extension_script = '<script async custom-element="amp-pan-zoom" src="https://cdn.ampproject.org/v0/amp-pan-zoom-0.1.js"></script>';

    //Base URL of the images
    private $base_url;

    //Time limit for acquiring image dimensions in seconds
    private $timeout;

    public function __construct($base_url = '', $timeout = 10)
    {
        $this->base_url = $base_url;
        $this->timeout = $timeout;
    }
    
    public function convert(\DOMDocument $doc)
    {
        $query = '//img';

        $xpath = new \DOMXPath($doc);

        $entries = $xpath->query($query);

        $allowed_attributes = ['src', 'alt', 'srcset', 'sizes'];
        
        foreach ($entries as $tag) {
            if ($tag->hasAttribute('src')) {
                if (!$tag->hasAttribute('width') || !$tag->hasAttribute('height')) {
                   
                    $imageSize = ImageSize::getImageSize(
                        $this->base_url,
                        $tag->getAttribute('src'),
                        $this->timeout
                    );
                    
                    if ($imageSize) {
                        $width = $imageSize['width'];
                        $height = $imageSize['height'];
                    } else {
                        $tag->parentNode->removeChild($tag);
                        continue;
                    }
                } else {
                    $width = $tag->getAttribute('width');
                    $height = $tag->getAttribute('height');
                }

                if (!$this->necessary_scripts) {
                    $this->necessary_scripts[] = $this->extension_script;
                }

                $img = $doc->createElement('amp-img');

                $panzoom = $doc->createElement('amp-pan-zoom');

                foreach ($allowed_attributes as $attribute) {
                    if ($tag->hasAttribute($attribute)) {
                        $img->setAttribute($attribute, $tag->getAttribute($attribute));
                    }
                }

                $img->setAttribute('layout', 'fill');

                $panzoom->setAttribute('width', $width);
                $panzoom->setAttribute('height', $height);
                $panzoom->setAttribute('layout', 'responsive');

                $panzoom->appendChild($img);

                $tag->parentNode->replaceChild($panzoom, $tag);
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
