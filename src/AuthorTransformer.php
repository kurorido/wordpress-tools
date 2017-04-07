<?php

namespace Roliroli\WordpressTools;

use Roliroli\WordpressTools\Models\Post;
use Roliroli\WordpressTools\Models\Options;

class AuthorTransformer extends Transformer
{
    private $columns = ['ID', 'nickname', 'email', 'description', 'avatar'];

    public function transform($items)
    {
        $transformed = [];

        foreach($items as $item)
        {
            $transformed[] = $this->transformSingle($item);
        }

        return $transformed;
    }

    public function transformSingle($item)
    {
        if(!is_array($item)) {
            $item = $item->toArray();
        }

        $item = $this->transformAvatar($item);
        $item = $this->transformBasic($item);
        $item = $this->filter($item, $this->columns);
        return $item;
    }

    private function transformBasic($item)
    {
        // Go through all meta (however, we only need description now)
        foreach($item['meta'] as $meta) {
            if($meta['meta_key'] == 'description') { // get description
                $item['description'] = $meta['meta_value'];
            }
        }
        return $item;
    }

    private function transformAvatar($item)
    {
        $hasAvatar = false;
        $metaAuthor = $item['meta'];

        foreach($metaAuthor as $meta)
        {
            // User Avatar Plugin
            // https://tw.wordpress.org/plugins/wp-user-avatar/
            if($meta['meta_key'] == 'wp_user_avatar')
            {
                $avatar_id = $meta['meta_value'];
                $hasAvatar = true;
            }
        }

        if($hasAvatar) {
            $postRef = Post::find($avatar_id);
            $avatar = $this->getImageFromMetaList($postRef["meta"]);
        } else {
            // Check default avatar
            $defaultAvatar = Options::get('avatar_default_wp_user_avatar');
            if($defaultAvatar != "") {
                $postRef = Post::find(intval($defaultAvatar));
                $avatar = $this->getImageFromMetaList($postRef["meta"]);
            } else {
                $avatar = null;
            }
        }

        if($avatar != null) {
            $avatar['title'] = $postRef['title'];
            $avatar['description'] = $postRef['excerpt'];
            $avatar['content'] = $postRef['content'];
        }

        $item['avatar'] = $avatar;

        return $item;
    }
}