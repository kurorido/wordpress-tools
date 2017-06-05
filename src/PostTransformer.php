<?php

namespace Roliroli\WordpressTools;

use Thunder\Shortcode\ShortcodeFacade;
use Thunder\Shortcode\Shortcode\ShortcodeInterface;
use Sunra\PhpSimple\HtmlDomParser;
use Carbon\Carbon;
use Roliroli\WordpressTools\Models\Post;

class PostTransformer extends Transformer
{

    private $columns = [
        'ID',
        'post_date',
        'post_content',
        'post_title',
        'post_excerpt',
        'post_name',
        'post_modified',
        'slug',
        'author_id',
        'author',
        'feature_image',
        'tag',
        'category',
        'main_category'
    ];

    private $options = [];

    public function __construct($options=[])
    {
        $this->options = $options;
    }

    public function transformSingle($item)
    {
        if(!is_array($item)) {
            $item = $item->toArray();
        }

        $item = $this->transformPostContent($item);
        $item = $this->transformAuthor($item);
        $item = $this->transformTerm($item);
        $item = $this->transformThumbnail($item);
        $item = $this->transformToDateTime($item);
        $item = $this->filter($item, $this->columns);
        return $item;
    }

    private function transformToDateTime($item)
    {
        $item['post_date'] = new Carbon($item['post_date_gmt']);
        $item['post_date'] = $item['post_date']->toIso8601String();
        $item['post_modified'] = new Carbon($item['post_modified_gmt']);
        $item['post_modified'] = $item['post_modified']->toIso8601String();
        return $item;
    }

    private function transformPostContent($item)
    {
        // $order = array("\r\n", "\n", "\r");
        // $replace = '<br/>';

        // $item['post_content'] = str_replace($order, $replace, $item['post_content']);

        // 沒有內容的情況下就直接回傳
        if (!isset($item['post_content'])) {
            $item['post_content'] = '';
            return $item;
        }

        $item['post_content'] = wpautop($item['post_content']);

        // shortcode [caption] => figure
        // [caption id="attachment_19418" align="alignnone" width="300"]<img class="size-medium wp-image-19418" src="http://www.goeducation.com.tw/wp-content/uploads/2016/04/o-BUSINESS-MEETING-facebook-300x150.jpg" alt="Financial planning" width="300" height="150" /> Financial planning[/caption]
        // <figure id="attachment_19418" style="width: 300px" class="wp-caption alignnone"><img class="size-medium wp-image-19418" src="http://www.goeducation.com.tw/wp-content/uploads/2016/04/o-BUSINESS-MEETING-facebook-300x150.jpg" alt="Financial planning" width="300" height="150"><figcaption class="wp-caption-text">Financial planning</figcaption></figure>

        $captionShortcode = new ShortcodeFacade();
        $captionShortcode->addHandler('caption', function(ShortcodeInterface $s) {
            $dom = HtmlDomParser::str_get_html($s->getContent('name'));
            return sprintf('<pre><figure> %s <figcaption> %s </figcaption></figure></pre>', $dom->find('img')[0]->outertext, $dom->plaintext);
        });

        $item['post_content'] = $captionShortcode->process($item['post_content']);

        // [embed]https://www.youtube.com/watch?v=UVHyS8UbiOE[/embed]
        // <iframe src="https://www.youtube.com/embed/UVHyS8UbiOE?feature=oembed" frameborder="0" allowfullscreen="" id="fitvid503643"></iframe>
        $embeddedShortcode = new ShortcodeFacade();
        $embeddedShortcode->addHandler('embed', function (ShortcodeInterface $s) {
            $dom = HtmlDomParser::str_get_html($s->getContent('name'));
            $url = parse_url($dom->plaintext);
            if (!isset($url['query'])) return '';
            parse_str($url['query'], $params);
            return sprintf('<div class="video-container"><iframe src="https://www.youtube.com/embed/%s?feature=oembed" frameborder="0" allowfullscreen=""></iframe></div>', $params['v']);
        });

        $item['post_content'] = $embeddedShortcode->process($item['post_content']);
        // end

        if(array_get($this->options, 'amp', false)) {
            $item['post_content'] = AmpUtils::amp($item['post_content']);
            // 直接砍掉沒辦法處理的 img tag
            $item['post_content'] = preg_replace("/<img[^>]+\>/i", "(image) ", $item['post_content']);
        }

        return $item;
    }

    private function transformAuthor($item)
    {
        if (!isset($item['author'])) {
            return $item;
        }

        $item['author'] = TransformerFactory::buildAuthorTransformer()->transformSingle($item['author']);

        return $item;
    }

    private function transformTerm($item)
    {
        $cats = [];
        $tags = [];

        if(isset($item['terms']['category']))
        {
            foreach($item['terms']['category'] as $key => $value)
            {
                $cats[] = [
                    'slug' => $key,
                    'name' => $value
                ];
            }
        } else {
            $cats[] = 'Uncategorized';
        }

        if(isset($item['terms']['tag']))
        {
            foreach($item['terms']['tag'] as $key => $value) {
                $tags[] = [
                    'slug' => $key,
                    'name' => $value
                ];
            }
        }

        $item['main_category'] = $cats[0];

        $item['category'] = $cats;
        $item['tag'] = $tags;

        return $item;
    }

    private function transformThumbnail($item)
    {
        if($item['thumbnail'] != null) {
            $post_id = intval($item['thumbnail']['meta_value']);
            $postRef = Post::find($post_id);
            $image = $this->getImageFromMetaList($postRef["meta"]);
            $image['title'] = $postRef['title'];
            $image['description'] = $postRef['excerpt'];
            $image['content'] = $postRef['content'];

            $item['feature_image'] = $image;
        } else {
            $item['feature_image'] = null;
        }

        return $item;
    }

    // also transform thumbnail here
    private function transformAttachment($item)
    {
        if(count($item['attachment']) == 0) {
            $item['feature_image'] = null;
        } else {
            // go throught all attachment, find that can build image
            foreach($item['attachment'] as $attachment)
            {

                $image = $this->getImageFromMetaList($attachment['meta']);

                if($image != null) {

                    $image['title'] = $attachment['title'];
                    $image['content'] = $attachment['content'];
                    $image['description'] = $attachment['excerpt'];

                    $item['feature_image'] = $image;
                    break;
                }
            }
        }

        return $item;
    }

}