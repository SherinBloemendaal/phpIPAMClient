<?php

declare(strict_types=1);

namespace SherinBloemendaal\PhpIPAMClient;

use SherinBloemendaal\PhpIPAMClient\Connection\Connection;

function phpipamMakeURL(string $url, string $scheme = 'https://'): string
{
    $url = trim($url);
    // Remove all to // like http:// or https://
    $url = preg_replace('/^.+\\/\\//', '', $url);
    // Set scheme
    //	$url = parse_url($url, PHP_URL_SCHEME) === null ? $scheme . $url : $url;
    $url = $scheme.$url;
    // Add last slash if not set
    $url = phpipamAddLastSlash($url);
    // check if add the end is api/ given
    if ('api/' !== substr($url, -4)) {
        $url .= 'api/';
    }

    return $url;
}

function phpipamAddLastSlash(string $value): string
{
    if ('/' !== substr($value, -1)) {
        $value .= '/';
    }

    return $value;
}

function phpipamConnection(): Connection
{
    return Connection::getInstance();
}
