# AMP ⚡ converter library

[![Build Status](https://travis-ci.com/magyarandras/amp-converter.svg?branch=master)](https://travis-ci.com/magyarandras/amp-converter)

A library to convert HTML articles, blog posts or similar content to [AMP (Accelerated Mobile Pages)](https://amp.dev).

**Note**: This library is not intended to convert entire HTML documents if you want to convert an entire page you should use a more advanced library, for example: [Lullabot/amp-library](https://github.com/Lullabot/amp-library/)

  
## Currently supported elments:
* [x] amp-img
* [x] amp-img in amp-pan-zoom
* [x] amp-img with lightbox
* [x] amp-video
* [x] amp-audio
* [x] amp-iframe
* [x] amp-youtube
* [x] amp-facebook
* [x] amp-instagram
* [x] amp-twitter
* [x] amp-pinterest
* [x] amp-playbuzz
* [x] amp-gist(Github gist embed)
* [x] amp-vimeo(You can use amp-iframe instead)
* [ ] amp-soundcloud(You can use amp-iframe instead)
* [x] amp-vk
* [x] amp-imgur
* [x] amp-dailymotion
* [x] amp-gfycat

## Usage:

Simple example:

```php
use magyarandras\AMPConverter\Converter;

$converter = new Converter();

//Load default converters
$converter->loadDefaultConverters();

//Convert html to amp html
$amphtml = $converter->convert($html);

//Get the necessary amp components
$amp_scripts = $converter->getScripts();

print_r($amphtml);
print_r($amp_scripts);
```

You can specify which converters to use by loading them manually:

```php
use magyarandras\AMPConverter\Converter;

use magyarandras\AMPConverter\TagConverter\AMPImgZoom;
use magyarandras\AMPConverter\TagConverter\AMPYoutube;

//PHP 7+ syntax:
//use magyarandras\AMPConverter\TagConverter\{AMPImgZoom,AMPYoutube}

$converter = new Converter();

$converter->addConverter(new AMPImgZoom());
$converter->addConverter(new AMPYoutube());

$amphtml = $converter->convert($html);

$amp_scripts = $converter->getScripts();

print_r($amphtml);
print_r($amp_scripts);

```