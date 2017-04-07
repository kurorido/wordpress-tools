<?php

namespace Roliroli\WordpressTools;

class TransformerFactory
{
    public static function buildPostTransformer($options=[])
    {
        return new PostTransformer($options);
    }

    public static function buildCategoryTransformer()
    {
        return new CategoryTransformer();
    }

    public static function buildTagTransformer()
    {
        return new CategoryTransformer();
    }

    public static function buildAuthorTransformer()
    {
        return new AuthorTransformer();
    }
}