<?php

namespace Roliroli\WordpressTools\Models;

use Corcel\Model\Taxonomy as Corcel;

class Category extends Corcel
{
    protected $connection = 'wordpress';
    protected $taxonomy = 'category';
}