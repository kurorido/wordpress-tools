<?php // File: app/Category.php

namespace Roliroli\WordpressTools\Models;

use Corcel\TermTaxonomy as Corcel;

class Tag extends Corcel
{
    protected $taxonomy = 'post_tag';
}