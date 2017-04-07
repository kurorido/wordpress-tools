<?php

namespace Roliroli\WordpressTools\Models;

use Corcel\Post as Corcel;

class Post extends Corcel
{
    public function scopePost($query)
    {
        return $query->with('author')->with('attachment')->type('post')->orderBy('post_date', 'desc')->published();
    }

    public function scopeWpAuthor($query, $author)
    {
        return $this->scopePost($query)->where('post_author', $author);
    }

    public function scopeCategory($query, $category)
    {
        return $this->scopePost($query)->taxonomy('category', $category);
    }

    public function scopeTag($query, $tag)
    {
        return $this->scopePost($query)->taxonomy('post_tag', $tag);
    }
}