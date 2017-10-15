<?php // File: app/Category.php

namespace Roliroli\WordpressTools\Models;

use Corcel\Model\Post as Corcel;

class Tag extends Corcel
{
    protected $connection = 'wordpress';
    protected $taxonomy = 'post_tag';
}