<?php

namespace Roliroli\WordpressTools;

class CategoryTransformer extends Transformer
{
    private $columns = [
        'description',
        'term'
    ];

    public function transform($items)
    {
        $transformed = [];

        for($i = 0; $i < count($items); $i++)
        {
            $transformed[] = $this->transformSingle($items[$i]);
        }

        return $transformed;
    }

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