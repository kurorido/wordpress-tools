<?php

namespace Roliroli\WordpressTools\Models;

use Corcel\TermTaxonomy as Corcel;

class Category extends Corcel
{
    protected $connection = 'wordpress';
    protected $taxonomy = 'category';
}