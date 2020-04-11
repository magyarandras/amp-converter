<?php
namespace magyarandras\AMPConverter\TagConverter;

use magyarandras\AMPConverter\TagConverterInterface;

class AMPPlaybuzz implements TagConverterInterface
{
    private $necessary_scripts = [];

    private $extension_script = '<script async custom-element="amp-playbuzz" src="https://cdn.ampproject.org/v0/amp-playbuzz-0.1.js"></script>';

    public function convert(\DOMDocument $doc)
    {
        $query = '//div[@class="playbuzz" and @data-id]';

        $xpath = new \DOMXPath($doc);

        $entries = $xpath->query($query);

        if ($entries->length > 0) {
            $this->necessary_scripts[] = $this->extension_script;
        }

        foreach ($entries as $tag) {
            if ($tag->hasAttribute('data-id')) {
                $playbuzz = $doc->createElement('amp-playbuzz');

                $playbuzz->setAttribute('height', '700');

                $playbuzz->setAttribute('data-item', $tag->getAttribute('data-id'));
                $playbuzz->setAttribute('data-share-buttons', 'true');

                $tag->parentNode->replaceChild($playbuzz, $tag);
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
