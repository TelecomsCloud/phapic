<?php
namespace Tc\Phapic;


class Filters
{
    static public function extractCodeFromQueryParams($uri)
    {
        $queryString = parse_url($uri, PHP_URL_QUERY);
        parse_str($queryString, $params);

        return $params['code'];
    }
}