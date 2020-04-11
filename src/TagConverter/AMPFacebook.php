<?php
namespace magyarandras\AMPConverter\TagConverter;

use magyarandras\AMPConverter\TagConverterInterface;

class AMPFacebook implements TagConverterInterface
{
    private $necessary_scripts = [];

    private $extension_script = '<script async custom-element="amp-facebook" src="https://cdn.ampproject.org/v0/amp-facebook-0.1.js"></script>';

    public function convert(\DOMDocument $doc)
    {
        $converted_doc = $this->convertScriptEmbeds($doc);
        $converted_doc = $this->convertIframeEmbeds($doc);

        return $converted_doc;
    }

    private function convertIframeEmbeds(\DOMDocument $doc)
    {
        $query = '//iframe[php:functionString(\'preg_match\', \'/\/plugins\/(video|post).php\?href=([^&]*)/i\', @src) > 0]';

        $xpath = new \DOMXPath($doc);

        $xpath->registerNamespace("php", "http://php.net/xpath");
        $xpath->registerPhpFunctions('preg_match');

        $entries = $xpath->query($query);

        if ($entries->length > 0 && !$this->necessary_scripts) {
            $this->necessary_scripts[] = $this->extension_script;
        }

        foreach ($entries as $tag) {
            preg_match('/\/plugins\/(video|post).php\?href=([^&]*)/i', $tag->getAttribute('src'), $id_match);
            
            $type = $id_match[1];
            $post_url = urldecode($id_match[2]);

            $fb_embed = $doc->createElement('amp-facebook');

            if ($type == 'video') {
                $embed_type = 'video';
            } else {
                $embed_type = 'post';
            }

            $fb_embed->setAttribute('width', '552');
            $fb_embed->setAttribute('height', '310');
            $fb_embed->setAttribute('layout', 'responsive');

            $fb_embed->setAttribute('data-href', $post_url);
            $fb_embed->setAttribute('data-embed-as', $embed_type);

            $tag->parentNode->replaceChild($fb_embed, $tag);
        }

        return $doc;
    }

    private function convertScriptEmbeds(\DOMDocument $doc)
    {
        $query = '//div[(@class="fb-post" or @class="fb-video" or @class="fb-comment-embed") and @data-href]';

        $xpath = new \DOMXPath($doc);

        $entries = $xpath->query($query);

        if ($entries->length > 0 && !$this->necessary_scripts) {
            $this->necessary_scripts[] = $this->extension_script;
        }

        foreach ($entries as $tag) {
            if ($tag->hasAttribute('data-href')) {
                $fb_embed = $doc->createElement('amp-facebook');

                if ($tag->getAttribute('class') == 'fb-video') {
                    $embed_type = 'video';
                } elseif ($tag->getAttribute('class') == 'fb-comment-embed') {
                    $embed_type = 'comment';
                } else {
                    $embed_type = 'post';
                }

                $fb_embed->setAttribute('width', '552');
                $fb_embed->setAttribute('height', '310');
                $fb_embed->setAttribute('layout', 'responsive');

                $fb_embed->setAttribute('data-href', $tag->getAttribute('data-href'));
                $fb_embed->setAttribute('data-embed-as', $embed_type);

                $tag->parentNode->replaceChild($fb_embed, $tag);
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
