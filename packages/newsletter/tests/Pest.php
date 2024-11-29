<?php

function serverUrl(?string $path = null, bool $full = false): string
{
    $path = implode('/', [Config::get('lunar-api.general.route_prefix'), 'v1', ltrim($path, '/')]);

    return $path;
}
