<?php

namespace spec\magyarandras\AMPConverter;

use magyarandras\AMPConverter\Converter;

use magyarandras\AMPConverter\TagConverter\AMPImg;
use magyarandras\AMPConverter\TagConverter\AMPImgZoom;
use magyarandras\AMPConverter\TagConverter\AMPImgLightbox;
use magyarandras\AMPConverter\TagConverter\AMPYoutube;
use magyarandras\AMPConverter\TagConverter\AMPIframe;
use magyarandras\AMPConverter\TagConverter\AMPVideo;
use magyarandras\AMPConverter\TagConverter\AMPAudio;
use magyarandras\AMPConverter\TagConverter\AMPFacebook;
use magyarandras\AMPConverter\TagConverter\AMPPinterest;
use magyarandras\AMPConverter\TagConverter\AMPPlaybuzz;
use magyarandras\AMPConverter\TagConverter\AMPGist;
use magyarandras\AMPConverter\TagConverter\AMPTwitter;
use magyarandras\AMPConverter\TagConverter\AMPInstagram;
use magyarandras\AMPConverter\TagConverter\AMPImgur;
use magyarandras\AMPConverter\TagConverter\AMPVk;
use magyarandras\AMPConverter\TagConverter\AMPVimeo;
use magyarandras\AMPConverter\TagConverter\AMPDailymotion;
use magyarandras\AMPConverter\TagConverter\AMPGfycat;


use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ConverterSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(Converter::class);
    }

    public function it_converts_html_to_amp()
    {
        $this->addConverter(new AMPImg());
        $this->convert('<div style="text-align:center;"><p>text</p><img src="img.jpg" width="500" height="420"></div>')
        ->shouldReturn('<div><p>text</p><amp-img src="img.jpg" width="500" height="420" layout="responsive"></amp-img></div>');
    }

    public function it_removes_prohibited_attributes()
    {
        $this->convert('<div onmousedown="event();" style="text-align:center;"><p>hh</p><a href="javascript:alert(\'Hello\');">Hello!</a></div>')
        ->shouldReturn('<div><p>hh</p></div>');
    }

    public function it_converts_img_to_ampimg()
    {
        $this->addConverter(new AMPImg());
        
        $this->convert('<img src="/img.jpg" width="800" height="420" alt="Sample image">')
        ->shouldReturn('<amp-img src="/img.jpg" width="800" height="420" alt="Sample image" layout="responsive"></amp-img>');
        
        $this->getScripts()->shouldReturn([]);
    }

    public function it_converts_img_to_ampimg_with_zoom()
    {
        $this->addConverter(new AMPImgZoom());
        
        $this->convert('<img src="/img.jpg" width="800" height="420" alt="Sample image">')
        ->shouldReturn('<amp-pan-zoom width="800" height="420" layout="responsive"><amp-img src="/img.jpg" alt="Sample image" layout="fill"></amp-img></amp-pan-zoom>');
        
        $this->getScripts()->shouldReturn([
            '<script async custom-element="amp-pan-zoom" src="https://cdn.ampproject.org/v0/amp-pan-zoom-0.1.js"></script>'
        ]);
    }

    public function it_converts_img_to_ampimg_with_lightbox()
    {
        $this->addConverter(new AMPImgLightbox());
        
        $this->convert('<img src="/img.jpg" width="800" height="420" alt="Sample image">')
        ->shouldReturn('<amp-img src="/img.jpg" width="800" height="420" alt="Sample image" layout="responsive" lightbox></amp-img>');
        
        $this->getScripts()->shouldReturn([
            '<script async custom-element="amp-lightbox-gallery" src="https://cdn.ampproject.org/v0/amp-lightbox-gallery-0.1.js"></script>'
        ]);
    }

    public function it_converts_youtube_videos()
    {
        $this->addConverter(new AMPYoutube());

        $this->convert('<iframe width="560" height="315" src="https://www.youtube.com/embed/lBTCB7yLs8Y" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>')
        ->shouldReturn('<amp-youtube data-videoid="lBTCB7yLs8Y" width="480" height="270" layout="responsive"></amp-youtube>');

        $this->getScripts()->shouldReturn([
            '<script async custom-element="amp-youtube" src="https://cdn.ampproject.org/v0/amp-youtube-0.1.js"></script>'
        ]);
    }

    public function it_converts_iframe_to_ampiframe()
    {
        $this->addConverter(new AMPIframe());

        $this->convert('<iframe src="https://player.vimeo.com/video/93199353?color=ff9933&title=0&byline=0&portrait=0&badge=0" width="640" height="360" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>')
        ->shouldReturn('<amp-iframe src="https://player.vimeo.com/video/93199353?color=ff9933&amp;title=0&amp;byline=0&amp;portrait=0&amp;badge=0" sandbox="allow-scripts allow-same-origin allow-popups" width="640" height="360" allowfullscreen layout="responsive"><p placeholder>Loading...</p></amp-iframe>');

        $this->getScripts()->shouldReturn([
            '<script async custom-element="amp-iframe" src="https://cdn.ampproject.org/v0/amp-iframe-0.1.js"></script>'
        ]);
    }

    public function it_converts_video_to_ampvideo()
    {
        $this->addConverter(new AMPVideo());

        $this->convert('<video width="320" height="240" controls>
<source src="movie.mp4" type="video/mp4">
<source src="movie.ogg" type="video/ogg">
</video>')
        ->shouldReturn('<amp-video width="320" height="240" layout="responsive" controls>
<source src="movie.mp4" type="video/mp4">
<source src="movie.ogg" type="video/ogg">
</amp-video>');

        $this->getScripts()->shouldReturn([
            '<script async custom-element="amp-video" src="https://cdn.ampproject.org/v0/amp-video-0.1.js"></script>'
        ]);
    }

    public function it_converts_audio_to_ampaudio()
    {
        $this->addConverter(new AMPAudio());

        $this->convert('<audio controls>
<source src="horse.ogg" type="audio/ogg">
<source src="horse.mp3" type="audio/mpeg">
</audio>')
        ->shouldReturn('<amp-audio controls>
<source src="horse.ogg" type="audio/ogg">
<source src="horse.mp3" type="audio/mpeg">
</amp-audio>');

        $this->getScripts()->shouldReturn([
            '<script async custom-element="amp-audio" src="https://cdn.ampproject.org/v0/amp-audio-0.1.js"></script>'
        ]);
    }

    public function it_converts_fb_posts_to_ampfacebook()
    {
        $this->addConverter(new AMPFacebook());

        $this->convert('<script async defer src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v3.2"></script><div class="fb-post" data-href="https://www.facebook.com/20531316728/posts/10154009990506729/" data-width="500"></div>')
        ->shouldReturn('<amp-facebook width="552" height="310" layout="responsive" data-href="https://www.facebook.com/20531316728/posts/10154009990506729/" data-embed-as="post"></amp-facebook>');

        $this->getScripts()->shouldReturn([
            '<script async custom-element="amp-facebook" src="https://cdn.ampproject.org/v0/amp-facebook-0.1.js"></script>'
        ]);
    }

    public function it_converts_facebook_iframe_post_embed_to_ampfacebook()
    {
        $this->addConverter(new AMPFacebook());

        $this->convert('<iframe src="https://www.facebook.com/plugins/post.php?href=https%3A%2F%2Fwww.facebook.com%2FEngineering%2Fposts%2F10157088947367200&width=500" width="500" height="548" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowTransparency="true" allow="encrypted-media"></iframe>')
        ->shouldReturn('<amp-facebook width="552" height="310" layout="responsive" data-href="https://www.facebook.com/Engineering/posts/10157088947367200" data-embed-as="post"></amp-facebook>');

        $this->getScripts()->shouldReturn([
            '<script async custom-element="amp-facebook" src="https://cdn.ampproject.org/v0/amp-facebook-0.1.js"></script>'
        ]);
    }

    public function it_converts_facebook_iframe_video_embed_to_ampfacebook()
    {
        $this->addConverter(new AMPFacebook());

        $this->convert('<iframe src="https://www.facebook.com/plugins/video.php?href=https%3A%2F%2Fwww.facebook.com%2FEngineering%2Fvideos%2F391155644961802%2F&show_text=0&width=560" width="560" height="314" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowTransparency="true" allowFullScreen="true"></iframe>')
        ->shouldReturn('<amp-facebook width="552" height="310" layout="responsive" data-href="https://www.facebook.com/Engineering/videos/391155644961802/" data-embed-as="video"></amp-facebook>');

        $this->getScripts()->shouldReturn([
            '<script async custom-element="amp-facebook" src="https://cdn.ampproject.org/v0/amp-facebook-0.1.js"></script>'
        ]);
    }

    public function it_converts_fb_video_to_ampfacebook()
    {
        $this->addConverter(new AMPFacebook());

        $this->convert('<div class="fb-video" data-href="https://www.facebook.com/facebook/videos/10153231379946729/" data-width="500" data-show-text="false">
        <div class="fb-xfbml-parse-ignore">
          <blockquote cite="https://www.facebook.com/facebook/videos/10153231379946729/">
            <a href="https://www.facebook.com/facebook/videos/10153231379946729/">How to Share With Just Friends</a>
            <p>How to share with just friends.</p>
            Posted by <a href="https://www.facebook.com/facebook/">Facebook</a> on Friday, December 5, 2014
          </blockquote>
        </div>
      </div>')
        ->shouldReturn('<amp-facebook width="552" height="310" layout="responsive" data-href="https://www.facebook.com/facebook/videos/10153231379946729/" data-embed-as="video"></amp-facebook>');

        $this->getScripts()->shouldReturn([
            '<script async custom-element="amp-facebook" src="https://cdn.ampproject.org/v0/amp-facebook-0.1.js"></script>'
        ]);
    }

    public function it_converts_fb_comment_to_ampfacebook()
    {
        $this->addConverter(new AMPFacebook());

        $this->convert('<div class="fb-comment-embed"
        data-href="https://www.facebook.com/zuck/posts/10102735452532991?comment_id=1070233703036185"
        data-width="500"></div>')
        ->shouldReturn('<amp-facebook width="552" height="310" layout="responsive" data-href="https://www.facebook.com/zuck/posts/10102735452532991?comment_id=1070233703036185" data-embed-as="comment"></amp-facebook>');

        $this->getScripts()->shouldReturn([
            '<script async custom-element="amp-facebook" src="https://cdn.ampproject.org/v0/amp-facebook-0.1.js"></script>'
        ]);
    }

    public function it_converts_pinterest_embed_to_amppinterest()
    {
        $this->addConverter(new AMPPinterest());

        $this->convert('<a data-pin-do="embedPin" data-pin-width="large" href="https://www.pinterest.com/pin/99360735500167749/"></a>')
        ->shouldReturn('<amp-pinterest width="552" height="310" layout="responsive" data-url="https://www.pinterest.com/pin/99360735500167749/" data-do="embedPin"></amp-pinterest>');

        $this->getScripts()->shouldReturn([
            '<script async custom-element="amp-pinterest" src="https://cdn.ampproject.org/v0/amp-pinterest-0.1.js"></script>'
        ]);
    }

    public function it_converts_playbuzz_embed_to_ampplaybuzz()
    {
        $this->addConverter(new AMPPlaybuzz());

        $this->convert('<script>(function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(d.getElementById(id))return;js=d.createElement(s);js.id=id;js.src=\'https://embed.playbuzz.com/sdk.js\';fjs.parentNode.insertBefore(js,fjs);}(document,\'script\',\'playbuzz-sdk\'));</script>
<div class="playbuzz" data-id="f2bc8619-8c50-44c3-80bb-8eda5a3497c0" data-show-share="false" data-show-info="false" data-comments="false"></div>')
        ->shouldReturn('<amp-playbuzz height="700" data-item="f2bc8619-8c50-44c3-80bb-8eda5a3497c0" data-share-buttons="true"></amp-playbuzz>');

        $this->getScripts()->shouldReturn([
            '<script async custom-element="amp-playbuzz" src="https://cdn.ampproject.org/v0/amp-playbuzz-0.1.js"></script>'
        ]);
    }

    public function it_converts_imgur_embeds_to_ampimgur()
    {
        $this->addConverter(new AMPImgur());

        $this->convert('<blockquote class="imgur-embed-pub" lang="en" data-id="XVMu7rB"><a href="//imgur.com/XVMu7rB">can&#39;t see? no problem</a></blockquote><script async src="//s.imgur.com/min/embed.js" charset="utf-8"></script>')
        ->shouldReturn('<amp-imgur data-imgur-id="XVMu7rB" width="540" height="670" layout="responsive"></amp-imgur>');

        $this->getScripts()->shouldReturn([
            '<script async custom-element="amp-imgur" src="https://cdn.ampproject.org/v0/amp-imgur-0.1.js"></script>'
        ]);
    }

    public function it_converts_gist_embed_to_ampgist()
    {
        $this->addConverter(new AMPGist());

        $this->convert('<script src="https://gist.github.com/fabpot/bad2044f8b7fb7c25860793f251a96b6.js"></script>')
        ->shouldReturn('<amp-gist data-gistid="bad2044f8b7fb7c25860793f251a96b6" layout="fixed-height" height="250"></amp-gist>');

        $this->getScripts()->shouldReturn([
            '<script async custom-element="amp-gist" src="https://cdn.ampproject.org/v0/amp-gist-0.1.js"></script>'
        ]);
    }

    public function it_converts_twitter_post_embed_to_amptwitter()
    {
        $this->addConverter(new AMPTwitter());

        $this->convert('<blockquote class="twitter-tweet" data-lang="en"><p lang="en" dir="ltr">Not just web pages need better UX. Here&#39;s how we bring <a href="https://twitter.com/AMPhtml?ref_src=twsrc%5Etfw">@AMPhtml</a> to other parts of the internet: <a href="https://t.co/hSqH3nKsr7">https://t.co/hSqH3nKsr7</a></p>&mdash; Paul Bakaus (@pbakaus) <a href="https://twitter.com/pbakaus/status/996439497804890112?ref_src=twsrc%5Etfw">May 15, 2018</a></blockquote><script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>')
        ->shouldReturn('<amp-twitter width="375" height="472" layout="responsive" data-tweetid="996439497804890112"></amp-twitter>');

        $this->getScripts()->shouldReturn([
            '<script async custom-element="amp-twitter" src="https://cdn.ampproject.org/v0/amp-twitter-0.1.js"></script>'
        ]);
    }

    public function it_converts_instagram_post_embed_to_ampinstagram()
    {
        $this->addConverter(new AMPInstagram());

        $this->convert('<blockquote class="instagram-media" data-instgrm-captioned data-instgrm-permalink="https://www.instagram.com/p/tsxp1hhQTG/?utm_source=ig_embed&amp;utm_medium=loading" data-instgrm-version="12" style=" background:#FFF; border:0; border-radius:3px; box-shadow:0 0 1px 0 rgba(0,0,0,0.5),0 1px 10px 0 rgba(0,0,0,0.15); margin: 1px; max-width:540px; min-width:326px; padding:0; width:99.375%; width:-webkit-calc(100% - 2px); width:calc(100% - 2px);"><div style="padding:16px;"> <a href="https://www.instagram.com/p/tsxp1hhQTG/?utm_source=ig_embed&amp;utm_medium=loading" style=" background:#FFFFFF; line-height:0; padding:0 0; text-align:center; text-decoration:none; width:100%;" target="_blank"> <div style=" display: flex; flex-direction: row; align-items: center;"> <div style="background-color: #F4F4F4; border-radius: 50%; flex-grow: 0; height: 40px; margin-right: 14px; width: 40px;"></div> <div style="display: flex; flex-direction: column; flex-grow: 1; justify-content: center;"> <div style=" background-color: #F4F4F4; border-radius: 4px; flex-grow: 0; height: 14px; margin-bottom: 6px; width: 100px;"></div> <div style=" background-color: #F4F4F4; border-radius: 4px; flex-grow: 0; height: 14px; width: 60px;"></div></div></div><div style="padding: 19% 0;"></div><div style="display:block; height:50px; margin:0 auto 12px; width:50px;"><svg width="50px" height="50px" viewBox="0 0 60 60" version="1.1" xmlns="https://www.w3.org/2000/svg" xmlns:xlink="https://www.w3.org/1999/xlink"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><g transform="translate(-511.000000, -20.000000)" fill="#000000"><g><path d="M556.869,30.41 C554.814,30.41 553.148,32.076 553.148,34.131 C553.148,36.186 554.814,37.852 556.869,37.852 C558.924,37.852 560.59,36.186 560.59,34.131 C560.59,32.076 558.924,30.41 556.869,30.41 M541,60.657 C535.114,60.657 530.342,55.887 530.342,50 C530.342,44.114 535.114,39.342 541,39.342 C546.887,39.342 551.658,44.114 551.658,50 C551.658,55.887 546.887,60.657 541,60.657 M541,33.886 C532.1,33.886 524.886,41.1 524.886,50 C524.886,58.899 532.1,66.113 541,66.113 C549.9,66.113 557.115,58.899 557.115,50 C557.115,41.1 549.9,33.886 541,33.886 M565.378,62.101 C565.244,65.022 564.756,66.606 564.346,67.663 C563.803,69.06 563.154,70.057 562.106,71.106 C561.058,72.155 560.06,72.803 558.662,73.347 C557.607,73.757 556.021,74.244 553.102,74.378 C549.944,74.521 548.997,74.552 541,74.552 C533.003,74.552 532.056,74.521 528.898,74.378 C525.979,74.244 524.393,73.757 523.338,73.347 C521.94,72.803 520.942,72.155 519.894,71.106 C518.846,70.057 518.197,69.06 517.654,67.663 C517.244,66.606 516.755,65.022 516.623,62.101 C516.479,58.943 516.448,57.996 516.448,50 C516.448,42.003 516.479,41.056 516.623,37.899 C516.755,34.978 517.244,33.391 517.654,32.338 C518.197,30.938 518.846,29.942 519.894,28.894 C520.942,27.846 521.94,27.196 523.338,26.654 C524.393,26.244 525.979,25.756 528.898,25.623 C532.057,25.479 533.004,25.448 541,25.448 C548.997,25.448 549.943,25.479 553.102,25.623 C556.021,25.756 557.607,26.244 558.662,26.654 C560.06,27.196 561.058,27.846 562.106,28.894 C563.154,29.942 563.803,30.938 564.346,32.338 C564.756,33.391 565.244,34.978 565.378,37.899 C565.522,41.056 565.552,42.003 565.552,50 C565.552,57.996 565.522,58.943 565.378,62.101 M570.82,37.631 C570.674,34.438 570.167,32.258 569.425,30.349 C568.659,28.377 567.633,26.702 565.965,25.035 C564.297,23.368 562.623,22.342 560.652,21.575 C558.743,20.834 556.562,20.326 553.369,20.18 C550.169,20.033 549.148,20 541,20 C532.853,20 531.831,20.033 528.631,20.18 C525.438,20.326 523.257,20.834 521.349,21.575 C519.376,22.342 517.703,23.368 516.035,25.035 C514.368,26.702 513.342,28.377 512.574,30.349 C511.834,32.258 511.326,34.438 511.181,37.631 C511.035,40.831 511,41.851 511,50 C511,58.147 511.035,59.17 511.181,62.369 C511.326,65.562 511.834,67.743 512.574,69.651 C513.342,71.625 514.368,73.296 516.035,74.965 C517.703,76.634 519.376,77.658 521.349,78.425 C523.257,79.167 525.438,79.673 528.631,79.82 C531.831,79.965 532.853,80.001 541,80.001 C549.148,80.001 550.169,79.965 553.369,79.82 C556.562,79.673 558.743,79.167 560.652,78.425 C562.623,77.658 564.297,76.634 565.965,74.965 C567.633,73.296 568.659,71.625 569.425,69.651 C570.167,67.743 570.674,65.562 570.82,62.369 C570.966,59.17 571,58.147 571,50 C571,41.851 570.966,40.831 570.82,37.631"></path></g></g></g></svg></div><div style="padding-top: 8px;"> <div style=" color:#3897f0; font-family:Arial,sans-serif; font-size:14px; font-style:normal; font-weight:550; line-height:18px;"> A bejegyzés megtekintése az Instagramon</div></div><div style="padding: 12.5% 0;"></div> <div style="display: flex; flex-direction: row; margin-bottom: 14px; align-items: center;"><div> <div style="background-color: #F4F4F4; border-radius: 50%; height: 12.5px; width: 12.5px; transform: translateX(0px) translateY(7px);"></div> <div style="background-color: #F4F4F4; height: 12.5px; transform: rotate(-45deg) translateX(3px) translateY(1px); width: 12.5px; flex-grow: 0; margin-right: 14px; margin-left: 2px;"></div> <div style="background-color: #F4F4F4; border-radius: 50%; height: 12.5px; width: 12.5px; transform: translateX(9px) translateY(-18px);"></div></div><div style="margin-left: 8px;"> <div style=" background-color: #F4F4F4; border-radius: 50%; flex-grow: 0; height: 20px; width: 20px;"></div> <div style=" width: 0; height: 0; border-top: 2px solid transparent; border-left: 6px solid #f4f4f4; border-bottom: 2px solid transparent; transform: translateX(16px) translateY(-4px) rotate(30deg)"></div></div><div style="margin-left: auto;"> <div style=" width: 0px; border-top: 8px solid #F4F4F4; border-right: 8px solid transparent; transform: translateY(16px);"></div> <div style=" background-color: #F4F4F4; flex-grow: 0; height: 12px; width: 16px; transform: translateY(-4px);"></div> <div style=" width: 0; height: 0; border-top: 8px solid #F4F4F4; border-left: 8px solid transparent; transform: translateY(-4px) translateX(8px);"></div></div></div></a> <p style=" margin:8px 0 0 0; padding:0 4px;"> <a href="https://www.instagram.com/p/tsxp1hhQTG/?utm_source=ig_embed&amp;utm_medium=loading" style=" color:#000; font-family:Arial,sans-serif; font-size:14px; font-style:normal; font-weight:normal; line-height:17px; text-decoration:none; word-wrap:break-word;" target="_blank">We’re putting the Weekend Hashtag Project on hold this weekend. Instead, we’re challenging people around the world to participate in the 10th Worldwide InstaMeet! Grab a few good friends or meet up with a larger group in your area and share your best photos and videos from the InstaMeet with the #WWIM10 hashtag for a chance to be featured on our blog Monday morning. Be sure to include the name of the location where your event took place along with the unique hashtag you&#39;ve chosen for your InstaMeet in your caption. Photo by @sun_shinealight</a></p> <p style=" color:#c9c8cd; font-family:Arial,sans-serif; font-size:14px; line-height:17px; margin-bottom:0; margin-top:8px; overflow:hidden; padding:8px 0 7px; text-align:center; text-overflow:ellipsis; white-space:nowrap;"><a href="https://www.instagram.com/instagram/?utm_source=ig_embed&amp;utm_medium=loading" style=" color:#c9c8cd; font-family:Arial,sans-serif; font-size:14px; font-style:normal; font-weight:normal; line-height:17px;" target="_blank"> Instagram</a> (@instagram) által megosztott bejegyzés, <time style=" font-family:Arial,sans-serif; font-size:14px; line-height:17px;" datetime="2014-10-03T18:00:13+00:00">Okt 3., 2014, időpont: 11:00 (PDT időzóna szerint)</time></p></div></blockquote><script async src="//www.instagram.com/embed.js"></script>')
        ->shouldReturn('<amp-instagram data-shortcode="tsxp1hhQTG" data-captioned width="400" height="400" layout="responsive"></amp-instagram>');

        $this->getScripts()->shouldReturn([
            '<script async custom-element="amp-instagram" src="https://cdn.ampproject.org/v0/amp-instagram-0.1.js"></script>'
        ]);
    }
    
    public function it_converts_instagram_igtv_embed_to_ampinstagram()
    {
        $this->addConverter(new AMPInstagram());

        $this->convert('<blockquote class="instagram-media" data-instgrm-captioned data-instgrm-permalink="https://www.instagram.com/tv/CWQZ-joJAZN/?utm_source=ig_embed&amp;utm_campaign=loading" data-instgrm-version="14" style=" background:#FFF; border:0; border-radius:3px; box-shadow:0 0 1px 0 rgba(0,0,0,0.5),0 1px 10px 0 rgba(0,0,0,0.15); margin: 1px; max-width:540px; min-width:326px; padding:0; width:99.375%; width:-webkit-calc(100% - 2px); width:calc(100% - 2px);"><div style="padding:16px;"> <a href="https://www.instagram.com/tv/CWQZ-joJAZN/?utm_source=ig_embed&amp;utm_campaign=loading" style=" background:#FFFFFF; line-height:0; padding:0 0; text-align:center; text-decoration:none; width:100%;" target="_blank"> <div style=" display: flex; flex-direction: row; align-items: center;"> <div style="background-color: #F4F4F4; border-radius: 50%; flex-grow: 0; height: 40px; margin-right: 14px; width: 40px;"></div> <div style="display: flex; flex-direction: column; flex-grow: 1; justify-content: center;"> <div style=" background-color: #F4F4F4; border-radius: 4px; flex-grow: 0; height: 14px; margin-bottom: 6px; width: 100px;"></div> <div style=" background-color: #F4F4F4; border-radius: 4px; flex-grow: 0; height: 14px; width: 60px;"></div></div></div><div style="padding: 19% 0;"></div> <div style="display:block; height:50px; margin:0 auto 12px; width:50px;"><svg width="50px" height="50px" viewBox="0 0 60 60" version="1.1" xmlns="https://www.w3.org/2000/svg" xmlns:xlink="https://www.w3.org/1999/xlink"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><g transform="translate(-511.000000, -20.000000)" fill="#000000"><g><path d="M556.869,30.41 C554.814,30.41 553.148,32.076 553.148,34.131 C553.148,36.186 554.814,37.852 556.869,37.852 C558.924,37.852 560.59,36.186 560.59,34.131 C560.59,32.076 558.924,30.41 556.869,30.41 M541,60.657 C535.114,60.657 530.342,55.887 530.342,50 C530.342,44.114 535.114,39.342 541,39.342 C546.887,39.342 551.658,44.114 551.658,50 C551.658,55.887 546.887,60.657 541,60.657 M541,33.886 C532.1,33.886 524.886,41.1 524.886,50 C524.886,58.899 532.1,66.113 541,66.113 C549.9,66.113 557.115,58.899 557.115,50 C557.115,41.1 549.9,33.886 541,33.886 M565.378,62.101 C565.244,65.022 564.756,66.606 564.346,67.663 C563.803,69.06 563.154,70.057 562.106,71.106 C561.058,72.155 560.06,72.803 558.662,73.347 C557.607,73.757 556.021,74.244 553.102,74.378 C549.944,74.521 548.997,74.552 541,74.552 C533.003,74.552 532.056,74.521 528.898,74.378 C525.979,74.244 524.393,73.757 523.338,73.347 C521.94,72.803 520.942,72.155 519.894,71.106 C518.846,70.057 518.197,69.06 517.654,67.663 C517.244,66.606 516.755,65.022 516.623,62.101 C516.479,58.943 516.448,57.996 516.448,50 C516.448,42.003 516.479,41.056 516.623,37.899 C516.755,34.978 517.244,33.391 517.654,32.338 C518.197,30.938 518.846,29.942 519.894,28.894 C520.942,27.846 521.94,27.196 523.338,26.654 C524.393,26.244 525.979,25.756 528.898,25.623 C532.057,25.479 533.004,25.448 541,25.448 C548.997,25.448 549.943,25.479 553.102,25.623 C556.021,25.756 557.607,26.244 558.662,26.654 C560.06,27.196 561.058,27.846 562.106,28.894 C563.154,29.942 563.803,30.938 564.346,32.338 C564.756,33.391 565.244,34.978 565.378,37.899 C565.522,41.056 565.552,42.003 565.552,50 C565.552,57.996 565.522,58.943 565.378,62.101 M570.82,37.631 C570.674,34.438 570.167,32.258 569.425,30.349 C568.659,28.377 567.633,26.702 565.965,25.035 C564.297,23.368 562.623,22.342 560.652,21.575 C558.743,20.834 556.562,20.326 553.369,20.18 C550.169,20.033 549.148,20 541,20 C532.853,20 531.831,20.033 528.631,20.18 C525.438,20.326 523.257,20.834 521.349,21.575 C519.376,22.342 517.703,23.368 516.035,25.035 C514.368,26.702 513.342,28.377 512.574,30.349 C511.834,32.258 511.326,34.438 511.181,37.631 C511.035,40.831 511,41.851 511,50 C511,58.147 511.035,59.17 511.181,62.369 C511.326,65.562 511.834,67.743 512.574,69.651 C513.342,71.625 514.368,73.296 516.035,74.965 C517.703,76.634 519.376,77.658 521.349,78.425 C523.257,79.167 525.438,79.673 528.631,79.82 C531.831,79.965 532.853,80.001 541,80.001 C549.148,80.001 550.169,79.965 553.369,79.82 C556.562,79.673 558.743,79.167 560.652,78.425 C562.623,77.658 564.297,76.634 565.965,74.965 C567.633,73.296 568.659,71.625 569.425,69.651 C570.167,67.743 570.674,65.562 570.82,62.369 C570.966,59.17 571,58.147 571,50 C571,41.851 570.966,40.831 570.82,37.631"></path></g></g></g></svg></div><div style="padding-top: 8px;"> <div style=" color:#3897f0; font-family:Arial,sans-serif; font-size:14px; font-style:normal; font-weight:550; line-height:18px;">View this post on Instagram</div></div><div style="padding: 12.5% 0;"></div> <div style="display: flex; flex-direction: row; margin-bottom: 14px; align-items: center;"><div> <div style="background-color: #F4F4F4; border-radius: 50%; height: 12.5px; width: 12.5px; transform: translateX(0px) translateY(7px);"></div> <div style="background-color: #F4F4F4; height: 12.5px; transform: rotate(-45deg) translateX(3px) translateY(1px); width: 12.5px; flex-grow: 0; margin-right: 14px; margin-left: 2px;"></div> <div style="background-color: #F4F4F4; border-radius: 50%; height: 12.5px; width: 12.5px; transform: translateX(9px) translateY(-18px);"></div></div><div style="margin-left: 8px;"> <div style=" background-color: #F4F4F4; border-radius: 50%; flex-grow: 0; height: 20px; width: 20px;"></div> <div style=" width: 0; height: 0; border-top: 2px solid transparent; border-left: 6px solid #f4f4f4; border-bottom: 2px solid transparent; transform: translateX(16px) translateY(-4px) rotate(30deg)"></div></div><div style="margin-left: auto;"> <div style=" width: 0px; border-top: 8px solid #F4F4F4; border-right: 8px solid transparent; transform: translateY(16px);"></div> <div style=" background-color: #F4F4F4; flex-grow: 0; height: 12px; width: 16px; transform: translateY(-4px);"></div> <div style=" width: 0; height: 0; border-top: 8px solid #F4F4F4; border-left: 8px solid transparent; transform: translateY(-4px) translateX(8px);"></div></div></div> <div style="display: flex; flex-direction: column; flex-grow: 1; justify-content: center; margin-bottom: 24px;"> <div style=" background-color: #F4F4F4; border-radius: 4px; flex-grow: 0; height: 14px; margin-bottom: 6px; width: 224px;"></div> <div style=" background-color: #F4F4F4; border-radius: 4px; flex-grow: 0; height: 14px; width: 144px;"></div></div></a><p style=" color:#c9c8cd; font-family:Arial,sans-serif; font-size:14px; line-height:17px; margin-bottom:0; margin-top:8px; overflow:hidden; padding:8px 0 7px; text-align:center; text-overflow:ellipsis; white-space:nowrap;"><a href="https://www.instagram.com/tv/CWQZ-joJAZN/?utm_source=ig_embed&amp;utm_campaign=loading" style=" color:#c9c8cd; font-family:Arial,sans-serif; font-size:14px; font-style:normal; font-weight:normal; line-height:17px; text-decoration:none;" target="_blank">A post shared by 9GAG: Go Fun The World (@9gag)</a></p></div></blockquote> <script async src="//www.instagram.com/embed.js"></script>')
        ->shouldReturn('<amp-instagram data-shortcode="CWQZ-joJAZN" data-captioned width="400" height="400" layout="responsive"></amp-instagram>');

        $this->getScripts()->shouldReturn([
            '<script async custom-element="amp-instagram" src="https://cdn.ampproject.org/v0/amp-instagram-0.1.js"></script>'
        ]);
    }
    
    public function it_converts_instagram_reel_embed_to_ampinstagram()
    {
        $this->addConverter(new AMPInstagram());

        $this->convert('<blockquote class="instagram-media" data-instgrm-captioned data-instgrm-permalink="https://www.instagram.com/reel/CcpoEOFl5d9/?utm_source=ig_embed&amp;utm_campaign=loading" data-instgrm-version="14" style=" background:#FFF; border:0; border-radius:3px; box-shadow:0 0 1px 0 rgba(0,0,0,0.5),0 1px 10px 0 rgba(0,0,0,0.15); margin: 1px; max-width:540px; min-width:326px; padding:0; width:99.375%; width:-webkit-calc(100% - 2px); width:calc(100% - 2px);"><div style="padding:16px;"> <a href="https://www.instagram.com/reel/CcpoEOFl5d9/?utm_source=ig_embed&amp;utm_campaign=loading" style=" background:#FFFFFF; line-height:0; padding:0 0; text-align:center; text-decoration:none; width:100%;" target="_blank"> <div style=" display: flex; flex-direction: row; align-items: center;"> <div style="background-color: #F4F4F4; border-radius: 50%; flex-grow: 0; height: 40px; margin-right: 14px; width: 40px;"></div> <div style="display: flex; flex-direction: column; flex-grow: 1; justify-content: center;"> <div style=" background-color: #F4F4F4; border-radius: 4px; flex-grow: 0; height: 14px; margin-bottom: 6px; width: 100px;"></div> <div style=" background-color: #F4F4F4; border-radius: 4px; flex-grow: 0; height: 14px; width: 60px;"></div></div></div><div style="padding: 19% 0;"></div> <div style="display:block; height:50px; margin:0 auto 12px; width:50px;"><svg width="50px" height="50px" viewBox="0 0 60 60" version="1.1" xmlns="https://www.w3.org/2000/svg" xmlns:xlink="https://www.w3.org/1999/xlink"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><g transform="translate(-511.000000, -20.000000)" fill="#000000"><g><path d="M556.869,30.41 C554.814,30.41 553.148,32.076 553.148,34.131 C553.148,36.186 554.814,37.852 556.869,37.852 C558.924,37.852 560.59,36.186 560.59,34.131 C560.59,32.076 558.924,30.41 556.869,30.41 M541,60.657 C535.114,60.657 530.342,55.887 530.342,50 C530.342,44.114 535.114,39.342 541,39.342 C546.887,39.342 551.658,44.114 551.658,50 C551.658,55.887 546.887,60.657 541,60.657 M541,33.886 C532.1,33.886 524.886,41.1 524.886,50 C524.886,58.899 532.1,66.113 541,66.113 C549.9,66.113 557.115,58.899 557.115,50 C557.115,41.1 549.9,33.886 541,33.886 M565.378,62.101 C565.244,65.022 564.756,66.606 564.346,67.663 C563.803,69.06 563.154,70.057 562.106,71.106 C561.058,72.155 560.06,72.803 558.662,73.347 C557.607,73.757 556.021,74.244 553.102,74.378 C549.944,74.521 548.997,74.552 541,74.552 C533.003,74.552 532.056,74.521 528.898,74.378 C525.979,74.244 524.393,73.757 523.338,73.347 C521.94,72.803 520.942,72.155 519.894,71.106 C518.846,70.057 518.197,69.06 517.654,67.663 C517.244,66.606 516.755,65.022 516.623,62.101 C516.479,58.943 516.448,57.996 516.448,50 C516.448,42.003 516.479,41.056 516.623,37.899 C516.755,34.978 517.244,33.391 517.654,32.338 C518.197,30.938 518.846,29.942 519.894,28.894 C520.942,27.846 521.94,27.196 523.338,26.654 C524.393,26.244 525.979,25.756 528.898,25.623 C532.057,25.479 533.004,25.448 541,25.448 C548.997,25.448 549.943,25.479 553.102,25.623 C556.021,25.756 557.607,26.244 558.662,26.654 C560.06,27.196 561.058,27.846 562.106,28.894 C563.154,29.942 563.803,30.938 564.346,32.338 C564.756,33.391 565.244,34.978 565.378,37.899 C565.522,41.056 565.552,42.003 565.552,50 C565.552,57.996 565.522,58.943 565.378,62.101 M570.82,37.631 C570.674,34.438 570.167,32.258 569.425,30.349 C568.659,28.377 567.633,26.702 565.965,25.035 C564.297,23.368 562.623,22.342 560.652,21.575 C558.743,20.834 556.562,20.326 553.369,20.18 C550.169,20.033 549.148,20 541,20 C532.853,20 531.831,20.033 528.631,20.18 C525.438,20.326 523.257,20.834 521.349,21.575 C519.376,22.342 517.703,23.368 516.035,25.035 C514.368,26.702 513.342,28.377 512.574,30.349 C511.834,32.258 511.326,34.438 511.181,37.631 C511.035,40.831 511,41.851 511,50 C511,58.147 511.035,59.17 511.181,62.369 C511.326,65.562 511.834,67.743 512.574,69.651 C513.342,71.625 514.368,73.296 516.035,74.965 C517.703,76.634 519.376,77.658 521.349,78.425 C523.257,79.167 525.438,79.673 528.631,79.82 C531.831,79.965 532.853,80.001 541,80.001 C549.148,80.001 550.169,79.965 553.369,79.82 C556.562,79.673 558.743,79.167 560.652,78.425 C562.623,77.658 564.297,76.634 565.965,74.965 C567.633,73.296 568.659,71.625 569.425,69.651 C570.167,67.743 570.674,65.562 570.82,62.369 C570.966,59.17 571,58.147 571,50 C571,41.851 570.966,40.831 570.82,37.631"></path></g></g></g></svg></div><div style="padding-top: 8px;"> <div style=" color:#3897f0; font-family:Arial,sans-serif; font-size:14px; font-style:normal; font-weight:550; line-height:18px;">View this post on Instagram</div></div><div style="padding: 12.5% 0;"></div> <div style="display: flex; flex-direction: row; margin-bottom: 14px; align-items: center;"><div> <div style="background-color: #F4F4F4; border-radius: 50%; height: 12.5px; width: 12.5px; transform: translateX(0px) translateY(7px);"></div> <div style="background-color: #F4F4F4; height: 12.5px; transform: rotate(-45deg) translateX(3px) translateY(1px); width: 12.5px; flex-grow: 0; margin-right: 14px; margin-left: 2px;"></div> <div style="background-color: #F4F4F4; border-radius: 50%; height: 12.5px; width: 12.5px; transform: translateX(9px) translateY(-18px);"></div></div><div style="margin-left: 8px;"> <div style=" background-color: #F4F4F4; border-radius: 50%; flex-grow: 0; height: 20px; width: 20px;"></div> <div style=" width: 0; height: 0; border-top: 2px solid transparent; border-left: 6px solid #f4f4f4; border-bottom: 2px solid transparent; transform: translateX(16px) translateY(-4px) rotate(30deg)"></div></div><div style="margin-left: auto;"> <div style=" width: 0px; border-top: 8px solid #F4F4F4; border-right: 8px solid transparent; transform: translateY(16px);"></div> <div style=" background-color: #F4F4F4; flex-grow: 0; height: 12px; width: 16px; transform: translateY(-4px);"></div> <div style=" width: 0; height: 0; border-top: 8px solid #F4F4F4; border-left: 8px solid transparent; transform: translateY(-4px) translateX(8px);"></div></div></div> <div style="display: flex; flex-direction: column; flex-grow: 1; justify-content: center; margin-bottom: 24px;"> <div style=" background-color: #F4F4F4; border-radius: 4px; flex-grow: 0; height: 14px; margin-bottom: 6px; width: 224px;"></div> <div style=" background-color: #F4F4F4; border-radius: 4px; flex-grow: 0; height: 14px; width: 144px;"></div></div></a><p style=" color:#c9c8cd; font-family:Arial,sans-serif; font-size:14px; line-height:17px; margin-bottom:0; margin-top:8px; overflow:hidden; padding:8px 0 7px; text-align:center; text-overflow:ellipsis; white-space:nowrap;"><a href="https://www.instagram.com/reel/CcpoEOFl5d9/?utm_source=ig_embed&amp;utm_campaign=loading" style=" color:#c9c8cd; font-family:Arial,sans-serif; font-size:14px; font-style:normal; font-weight:normal; line-height:17px; text-decoration:none;" target="_blank">A post shared by 9GAG: Go Fun The World (@9gag)</a></p></div></blockquote> <script async src="//www.instagram.com/embed.js"></script>')
        ->shouldReturn('<amp-instagram data-shortcode="CcpoEOFl5d9" data-captioned width="400" height="400" layout="responsive"></amp-instagram>');

        $this->getScripts()->shouldReturn([
            '<script async custom-element="amp-instagram" src="https://cdn.ampproject.org/v0/amp-instagram-0.1.js"></script>'
        ]);
    }

    public function it_converts_vk_embed_to_ampvk()
    {
        $this->addConverter(new AMPVk());

        $this->convert('<div id="vk_post_1_45616"></div><script type="text/javascript">
          (function(d, s, id) { var js, fjs = d.getElementsByTagName(s)[0]; if (d.getElementById(id)) return; js = d.createElement(s); js.id = id; js.src = "//vk.com/js/api/openapi.js?105"; fjs.parentNode.insertBefore(js, fjs); }(document, \'script\', \'vk_openapi_js\'));
          (function() {
            window.VK && VK.Widgets && VK.Widgets.Post && VK.Widgets.Post("vk_post_1_45616", 1, 45616, \'ZMk4b98xpQZMJJRXVsL1ig\', {width: 500}) || setTimeout(arguments.callee, 50);  }());
        </script>')
        ->shouldReturn('<amp-vk data-embedtype="post" data-owner-id="1" data-post-id="45616" data-hash="ZMk4b98xpQZMJJRXVsL1ig" width="500" height="300" layout="responsive"></amp-vk>');

        $this->getScripts()->shouldReturn([
            '<script async custom-element="amp-vk" src="https://cdn.ampproject.org/v0/amp-vk-0.1.js"></script>'
        ]);
    }

    public function it_converts_vimeo_embed_to_ampvimeo()
    {
        $this->addConverter(new AMPVimeo());

        $this->convert('<iframe src="https://player.vimeo.com/video/93199353?color=ff9933&title=0&byline=0&portrait=0&badge=0" width="640" height="360" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>')
        ->shouldReturn('<amp-vimeo data-videoid="93199353" width="500" height="281" layout="responsive"></amp-vimeo>');

        $this->getScripts()->shouldReturn([
            '<script async custom-element="amp-vimeo" src="https://cdn.ampproject.org/v0/amp-vimeo-0.1.js"></script>'
        ]);
    }

    public function it_converts_dailymotion_embed_to_ampdailymotion()
    {
        $this->addConverter(new AMPDailymotion());

        $this->convert('<iframe frameborder="0" width="480" height="270" src="https://www.dailymotion.com/embed/video/x7008g9" allowfullscreen allow="autoplay"></iframe>')
        ->shouldReturn('<amp-dailymotion data-videoid="x7008g9" width="480" height="270" layout="responsive"></amp-dailymotion>');

        $this->getScripts()->shouldReturn([
            '<script async custom-element="amp-dailymotion" src="https://cdn.ampproject.org/v0/amp-dailymotion-0.1.js"></script>'
        ]);
    }

    public function it_converts_gfycat_embed_to_ampgfycat()
    {
        $this->addConverter(new AMPGfycat());

        $this->convert('<div style=\'position:relative; padding-bottom:calc(56.42% + 44px)\'><iframe src=\'https://gfycat.com/ifr/NextRectangularHammerheadshark\' frameborder=\'0\' scrolling=\'no\' width=\'100%\' height=\'100%\' style=\'position:absolute;top:0;left:0;\' allowfullscreen></iframe></div><p><a href="https://gfycat.com/gifs/search/silicon+valley">from Silicon Valley GIFs</a> <a href="https://gfycat.com/nextrectangularhammerheadshark-silicon-valley">via Gfycat</a></p>')
        ->shouldReturn('<div><amp-gfycat data-gfyid="NextRectangularHammerheadshark" width="640" height="360" layout="responsive"></amp-gfycat></div><p><a href="https://gfycat.com/gifs/search/silicon+valley">from Silicon Valley GIFs</a> <a href="https://gfycat.com/nextrectangularhammerheadshark-silicon-valley">via Gfycat</a></p>');

        $this->getScripts()->shouldReturn([
            '<script async custom-element="amp-gfycat" src="https://cdn.ampproject.org/v0/amp-gfycat-0.1.js"></script>'
        ]);
    }
}
