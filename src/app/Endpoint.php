<?php

namespace Application;

use Framework\Http\{Path, Response};

function middleware(string $argument): string
{
    return 'Middleware: ' . $argument;
}

class Endpoint extends Response
{
    public function __invoke(): array
    {
        $this->charset('ASCII');
        return $this->response($this->url_path('style.css'));
    }

    public function media(Path $path): array
    {
        return $this->render_media($path->name);
    }

    public function page(Path $path): array
    {
        header('Header: Template');
        $this->context(lang: 'ru');

        return $this->render_template(substr($path->name, 0, -4) . 'php');
    }

    public function archive(Path $path, callable $middleware): string
    {
        $url = $this->url_for('archive', year: '2025', month: '05', day: '25');
//        $url = $this->url_for('archive', year: '2025', month: '05', error: '25');
        $url = PHP_EOL . PHP_EOL . var_export($url, true);

        $body = 'Path year: ' . $path->year;

        if (isset($path->month))
            $body .= ' month: ' . $path->month;

        if (isset($path->day))
            $body .= ' day: ' . $path->day;

        $current = PHP_EOL . PHP_EOL . $this->url->scheme() . '://' . $this->url->host() . $this->url->path();

        return $body . $url . PHP_EOL . PHP_EOL . $middleware('argument') . $current;
    }
}