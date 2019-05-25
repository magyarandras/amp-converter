<?php

namespace magyarandras\AMPConverter;

interface TagConverterInterface{

    public function convert(\DOMDocument $doc);

    public function getNecessaryScripts();

}