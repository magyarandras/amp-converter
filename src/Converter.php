<?php
namespace magyarandras\AMPConverter;

use magyarandras\AMPConverter\TagConverterInterface;

use Masterminds\HTML5;

class Converter {

    private $doc;

    private $prohibited_tags = [
        'script',
        'style',
        'frame',
        'frameset',
        'form',
        'legend',
        'input',
        'textarea',
        'select',
        'option',
        'map',
        'object',
        'param',
        'applet',
        'embed',
        'center',
        'font'
    ];

    //style attribute, event attributes and attributes deprecated in HTML 5
    private $prohibited_attributes = [
        'style' => '*',
        'width' => 'hr|table|td|th|col|colgroup|pre',
        'height' => 'hr|table|td|th|col|colgroup|pre',
        'valign' => 'col|colgroup|tbody|td|tfoot|th|thead|tr',
        'type' => 'li|ol|ul',
        'size' => 'hr',
        'rules' => 'table',
        'nowrap' => 'td|th',
        'noshade' => 'hr',
        'frame' =>'table',
        'compact' => 'dl|ol|ul',
        'clear' => 'br',
        'charoff' => 'col|colgroup|tbody|td|tfoot|th|thead|tr',
        'char' => 'col|colgroup|tbody|td|tfoot|th|thead|tr',
        'cellspacing' => 'table',
        'cellpadding' => 'table',
        'border' => '*',
        'bgcolor' => 'table|tr|td|th',
        'align' => '*',
        'scope' => 'td',
        'axis' => 'td',
        'abbr' => 'td',
        'charset' => 'a',
        'coords' => 'a',
        'shape' => 'a',
        'onkeydown' => '*',
        'onkeyup' => '*',
        'onkeypress' => '*',
        'onclick' => '*',
        'ondblclick' => '*',
        'onmousedown' => '*',
        'onmousemove' => '*',
        'onmouseout' => '*',
        'onmouseover' => '*',
        'onmouseup' => '*',
        'onmousewheel' => '*',
        'onwheel' => '*',
        'ondrag' => '*',
        'ondragend' => '*',
        'ondragenter' => '*',
        'ondragleave' => '*',
        'ondragover' => '*',
        'ondragstart' => '*',
        'ondrop' => '*',
        'onscroll' => '*',
        'ontoggle' => '*',
        'oncopy' => '*',
        'onpaste' => '*',
        'oncut' => '*'
    ];

    private $converters = [

    ];

    private $scripts = [

    ];

    private $base_url;

    public function __construct($base_url = ''){
        $this->base_url = $base_url;
    }

    public function loadDefaultConverters(){

        $built_in_converters = [
            new \magyarandras\AMPConverter\TagConverter\AMPImg($this->base_url),
            new \magyarandras\AMPConverter\TagConverter\AMPYoutube,
            new \magyarandras\AMPConverter\TagConverter\AMPVideo,
            new \magyarandras\AMPConverter\TagConverter\AMPAudio,
            new \magyarandras\AMPConverter\TagConverter\AMPFacebook,
            new \magyarandras\AMPConverter\TagConverter\AMPPinterest,
            new \magyarandras\AMPConverter\TagConverter\AMPPlaybuzz,
            new \magyarandras\AMPConverter\TagConverter\AMPImgur,
            new \magyarandras\AMPConverter\TagConverter\AMPGist,
            new \magyarandras\AMPConverter\TagConverter\AMPTwitter,
            new \magyarandras\AMPConverter\TagConverter\AMPInstagram,
            new \magyarandras\AMPConverter\TagConverter\AMPVk,
            //AMPIframe must be the last
            new \magyarandras\AMPConverter\TagConverter\AMPIframe
        ];

        
            foreach($built_in_converters as $converter){
                $this->converters[] = $converter;
            }
    }

    public function convert($html){

        $html5 = new HTML5([
            'disable_html_ns' => true
        ]);
        $this->doc = $html5->loadHTML($html);

        $this->removeIncorrectDimensionAttributes();

        foreach($this->converters as $converter){
            $result = $converter->convert($this->doc);
            $this->doc = $result;

            $this->scripts = array_unique(array_merge($this->scripts, $converter->getNecessaryScripts()));
        }

        $this->removeProhibitedTags();
        $this->removeProhibitedAttributes();

        $amphtml = $html5->saveHTML($this->doc);

        $to_replace = [
            '<!DOCTYPE html>', '<html>', '</html>', '<head>', '</head>', '<body>', '</body>'
        ];

        $amphtml = str_replace($to_replace, '', $amphtml);

        $amphtml = trim($amphtml, "\n\r\0\x0B");
        

        return $amphtml;


    }

    public function addConverter(TagConverterInterface $converter){
        //array_unshift($this->converters, $converter);
        $this->converters[] = $converter;
    }


    //Remove prohibited tags
    private function removeProhibitedTags(){

        $query = '//' . implode('|//', $this->prohibited_tags);

        $xpath = new \DOMXPath($this->doc);

        $entries = $xpath->query($query);
        
        foreach ($entries as $entry) {
            if ($entry->parentNode !== null) {
                $entry->parentNode->removeChild($entry);
            }
        }

    }

    //Remove width and height attributes if they provided in percent
    private function removeIncorrectDimensionAttributes(){
        $xpath = new \DOMXPath($this->doc);

        $entries = $xpath->query('//*[contains(@width, "%") or contains(@height, "%") or @width="auto" or @height="auto"]');

        foreach($entries as $entry){

                if($entry->hasAttribute('width')){
                    $entry->removeAttribute('width');
                }

                if($entry->hasAttribute('height')){
                    $entry->removeAttribute('height');
                }
                
        }

    }

    //Remove prohibited attributes
    private function removeProhibitedAttributes(){

        $xpath = new \DOMXPath($this->doc);

        foreach($this->prohibited_attributes as $attribute=>$tags){
            $entries = $xpath->query('//' . $tags . '[@'.$attribute.']');

            foreach($entries as $entry){
                $entry->removeAttribute($attribute);
            }

        }

        //Remove anchors with href="javascript:*"
        $invalid_a_tags = $xpath->query('//a[starts-with(@href, "javascript:")]');

        foreach($invalid_a_tags as $tag){
            $tag->parentNode->removeChild($tag);
        }

    }


    public function getScripts(){
        return $this->scripts;
    }


}