<?php
//vk_post_([0-9]+)_([0-9]+)

//VK.Widgets.Post[\n\r\s]*\([\n\r\s]*["|'](.*)["|'][\n\r\s]*,[\n\r\s]*([0-9]+)[\n\r\s]*,[\n\r\s]*([0-9]+)[\n\r\s]*,[\n\r\s]*["|']([0-9a-zA-Z]+)["|']
//preg_match('/VK.Widgets.Post[\n\r\s]*\([\n\r\s]*["|\'](.*)["|\'][\n\r\s]*,[\n\r\s]*([0-9]+)[\n\r\s]*,[\n\r\s]*([0-9]+)[\n\r\s]*,[\n\r\s]*["|\']([0-9a-zA-Z]+)["|\']/', $html, $data);

namespace magyarandras\AMPConverter\TagConverter;

use magyarandras\AMPConverter\TagConverterInterface;

class AMPVk implements TagConverterInterface
{
    private $necessary_scripts = [];

    private $extension_script = '<script async custom-element="amp-vk" src="https://cdn.ampproject.org/v0/amp-vk-0.1.js"></script>';

    public function convert(\DOMDocument $doc)
    {
        $query = '//script[contains(., "VK.Widgets.Post")]';
        $expression = '/VK.Widgets.Post[\n\r\s]*\([\n\r\s]*["|\'](.*)["|\'][\n\r\s]*,[\n\r\s]*([0-9]+)[\n\r\s]*,[\n\r\s]*([0-9]+)[\n\r\s]*,[\n\r\s]*["|\']([0-9a-zA-Z]+)["|\']/';
        

        $xpath = new \DOMXPath($doc);

        $xpath->registerNamespace("php", "http://php.net/xpath");
        $xpath->registerPhpFunctions('preg_match');

        $entries = $xpath->query($query);

        foreach ($entries as $tag) {
            if (preg_match($expression, $tag->nodeValue, $data)) {
                $tag_id = $data[1];
                $owner_id = $data[2];
                $post_id = $data[3];
                $hash = $data[4];

                $embed_container = $xpath->query('div[@id="'.$tag_id.'"]');

                if ($embed_container->length > 0) {
                    $vk_container = $embed_container[0];

                    $ampvk = $doc->createElement('amp-vk');
                    $ampvk->setAttribute('data-embedtype', 'post');
                    $ampvk->setAttribute('data-owner-id', $owner_id);
                    $ampvk->setAttribute('data-post-id', $post_id);
                    $ampvk->setAttribute('data-hash', $hash);
                    $ampvk->setAttribute('width', '500');
                    $ampvk->setAttribute('height', '300');
                    $ampvk->setAttribute('layout', 'responsive');

                    $vk_container->parentNode->replaceChild($ampvk, $vk_container);

                    if (!$this->necessary_scripts) {
                        $this->necessary_scripts[] = $this->extension_script;
                    }
                }
            }
        }

        return $doc;
    }

    public function getNecessaryScripts()
    {
        return $this->necessary_scripts;
    }
}
