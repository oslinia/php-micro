<?php

namespace Framework;

$dirname = dirname(__DIR__);

spl_autoload_register(function ($class) use ($dirname) {
    [$match, $path] = explode('\\', $class, 2);

    require $dirname . DIRECTORY_SEPARATOR . match ($match) {
            'Application' => str_replace('\\', DIRECTORY_SEPARATOR, 'src\\app\\' . $path),
            'Framework' => str_replace('\\', DIRECTORY_SEPARATOR, 'framework\\' . $path),
        } . '.php';
});

Foundation\Http::request($dirname);