<?php

use Framework\Http\Routing\Map;

use Application\Endpoint;
use function Application\middleware;

$map = new Map;

$map->rule('/', 'index');
$map->endpoint('index', Endpoint::class);

$map->rule('/media/{name}', 'media')
    ->where(name: '[a-z]+\\.[a-z]+');
$map->endpoint('media', Endpoint::class, 'media');

$map->rule('/page/{name}', 'page')
    ->where(name: '[a-z]+\\.[a-z]+');
$map->endpoint('page', Endpoint::class, 'page');

$map->rule('/archive/{year}', 'archive')
    ->where(year: '[0-9]{4}');
$map->rule('/archive/{year}/{month}', 'archive')
    ->where(year: '[0-9]{4}', month: '[0-9]{1,2}');
$map->rule('/archive/{year}/{month}/{day}', 'archive')
    ->where(year: '[0-9]{4}', month: '[0-9]{1,2}', day: '[0-9]{1,2}');
$map->endpoint('archive', Endpoint::class, 'archive')
    ->middleware(fn(string $argument) => middleware($argument));