<?php
namespace Tc\Phapic;


class Filters
{
    static public function extractQueryParams($uri)
    {
        return parse_url($uri, PHP_URL_PATH);
    }
}