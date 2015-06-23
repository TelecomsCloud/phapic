<?php
namespace Tc\Phapic;


class Filters
{
    static public function extractCodeFromQueryParams($uri)
    {
        $params = parse_url($uri, PHP_URL_QUERY);

        return $params['code'];
    }
}