<?php
namespace magyarandras\AMPConverter\TagConverter;

use magyarandras\AMPConverter\TagConverterInterface;

class AMPPinterest implements TagConverterInterface
{
    private $necessary_scripts = [];

    private $extension_script = '<script async custom-element="amp-pinterest" src="https://cdn.ampproject.org/v0/amp-pinterest-0.1.js"></script>';

    public function convert(\DOMDocument $doc)
    {
        $query = '//a[@data-pin-do="embedPin" and @href]';

        $xpath = new \DOMXPath($doc);

        $entries = $xpath->query($query);

        if ($entries->length > 0) {
            $this->necessary_scripts[] = $this->extension_script;
        }

        foreach ($entries as $tag) {
            if ($tag->hasAttribute('href')) {
                $pin_embed = $doc->createElement('amp-pinterest');

                $pin_embed->setAttribute('width', '552');
                $pin_embed->setAttribute('height', '310');
                $pin_embed->setAttribute('layout', 'responsive');

                $pin_embed->setAttribute('data-url', $tag->getAttribute('href'));
                $pin_embed->setAttribute('data-do', 'embedPin');

                $tag->parentNode->replaceChild($pin_embed, $tag);
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
