<?php

namespace Roliroli\WordpressTools;

use Lullabot\AMP\AMP;

class AmpUtils
{
    public static function amp($html)
    {
        $amp = new AMP();
        $amp->loadHtml($html);
        $amp_html = $amp->convertToAmpHtml();

        return $amp_html;
    }
}
