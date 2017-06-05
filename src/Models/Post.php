<?php

namespace Roliroli\WordpressTools\Models;

use Corcel\Post as Corcel;

class Post extends Corcel
{
    protected $connection = 'wordpress';

    private $noContentFields = [
        'ID', 'post_name', 'post_date', 'post_title', 'post_excerpt', 'post_type', 'post_date_gmt', 'post_modified_gmt'
    ];

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

    public function scopeNoPostContent($query)
    {
        return $query->select($this->noContentFields);
    }
}