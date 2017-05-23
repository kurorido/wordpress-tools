<?php

namespace Roliroli\WordpressTools;

class CategoryTransformer extends Transformer
{
    private $columns = [
        'description',
        'term'
    ];

    public function transformSingle($item)
    {
        $item = $this->filter($item->toArray(), $this->columns);
        $item = $this->transformCategory($item);
        return $item;
    }

    public function transformCategory($item)
    {
        $item['name'] = $item["term"]["name"];
        $item['slug'] = $item["term"]["slug"];

        unset($item["term"]);

        return $item;
    }
}