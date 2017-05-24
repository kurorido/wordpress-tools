<?php

namespace Roliroli\WordpressTools;

abstract class Transformer
{
    abstract public function transformSingle($item);

    public function transform($items)
    {
        $transformed = [];

        foreach($items as $item)
        {
            $transformed[] = $this->transformSingle($item);
        }

        return $transformed;
    }


    public function transformObjects($items)
    {
        $transformed = [];

        foreach($items as $item)
        {
            $transformed[] = $this->transformSingleObject($item);
        }

        return $transformed;
    }

    public function transformSingleObject($item)
    {
        return json_decode(json_encode($this->transformSingle($item)));
    }

    public function filter($item, $columns)
    {
        $_item = [];

        foreach($columns as $column)
        {
            if(isset($item[$column])) {
                $_item[$column] = $item[$column];
            } else {
                $_item[$column] = null;
            }
        }

        return $_item;
    }

    public function getImageFromMetaList($metaList)
    {
        if($metaList == null) {
            return null;
        }

        $item = [];
        $hasImage = false;
        $use_s3 = false;

        $image_alt = "";

        foreach($metaList as $meta) {
            if($meta['meta_key'] == '_wp_attachment_metadata') {
                $item['image'] = $meta['value'];
                $hasImage = true;
            } else if($meta['meta_key'] == 'amazonS3_info') {
                $item['s3_info'] = $meta['value'];
                $use_s3 = true;
            } else if($meta['meta_key'] == "_wp_attachment_image_alt") {
                $image_alt = $meta['value'];
            }
        }

        if($hasImage) {

            if (function_exists('config')) {
                $url_prefix = config('wordpress_tool.RESOURCE_URL');
            } else {
                $url_prefix = 'http://localhost/';
            }

            // if using WP Offload S3 plugin
            // https://wordpress.org/plugins/amazon-s3-and-cloudfront/
            if($use_s3) {

                // key:
                // goeducation-tw/2016/07/28101623/pexels-photo-46710-large.jpeg
                // file:
                // pexels-photo-46710-large-300x200.jpeg
                // result:
                // https://s3.amazonaws.com/goedu-main-media/goeducation-tw/2016/07/28101623/
                if (function_exists('config')) {
                    $url_prefix = config('wordpress_tool.S3_RESOURCE_URL');
                } else {
                    $url_prefix = 'http://localhost/';
                }

                $bucket = $item['s3_info']['bucket'];
                $key = $item['s3_info']['key'];
                $segments = explode('/', $key);
                $folder = array_pop($segments); // ignore return value
                $keyPrefix = implode('/', $segments);
                $url_prefix = $url_prefix . $bucket . "/" . $keyPrefix . "/";
            }

            // handle different size images
            if(isset($item['image']['sizes'])) {
                foreach($item['image']['sizes'] as $imageKey => $imageVal) {
                    $item['image']['sizes'][$imageKey]['source_url'] = $url_prefix . $imageVal['file'];
                }
            } else {
                $item['image']['sizes'] = [];
            }

            // handle origin size image
            $segments = explode('/', $item['image']['file']);
            $item['image']['source_url'] = $url_prefix . array_pop($segments);

            // build better human readable json
            $_item = $item['image'];
            $item['image'] = [
                'width' => $_item['width'],
                'height' => $_item['height'],
                'file' => $_item['file'],
                'source_url' => $_item['source_url'],
                'sizes' => $_item['sizes'],
                'alt' => $image_alt
            ];

        } else {
            $item['image'] = null;
        }

        return $item['image'];
    }

    public function setupImageAltTtitle($item, $imageItemKey='feature_image')
    {
        if(isset($item['thumbnail']) && $item['thumbnail'] != null) {
            $item[$imageItemKey]['alt'] = $item['thumbnail']['attachment']['alt'];
            $item[$imageItemKey]['title'] = $item['thumbnail']['attachment']['title'];
            $item[$imageItemKey]['description'] = $item['thumbnail']['attachment']['description'];
        }

        return $item;
    }
}