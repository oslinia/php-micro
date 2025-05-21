<?php

namespace Framework\Http;

use Framework\Foundation\{Http, Url};
use function Framework\Foundation\template;

use stdClass;

function url_path(string $name): string
{
    return Http::$env->path . $name;
}

function url_for(string ...$args): string
{
    return Http::build($args) ?? '';
}

class Response
{
    private null|string $encoding = null;
    private array|null $context = null;
    private stdClass $env;
    public Url $url;

    public function __construct()
    {
        $this->env =& Http::$env;
        $this->url = new Url;
    }

    public function url_path(string $name): string
    {
        return $this->env->path . $name;
    }

    public function url_for(string ...$args): null|string
    {
        return Http::build($args);
    }

    public function charset(string $encoding): void
    {
        $this->encoding = $encoding;
    }

    public function response(
        string      $body,
        int|null    $code = null,
        null|string $mimetype = null,
        null|string $encoding = null): array
    {
        return [$body, $code, $mimetype, $encoding ?? $this->encoding];
    }

    public function render_media(string $name, null|string $encoding = null): array
    {
        $filename = $this->env->media . $name;

        if (is_file($filename)) {
            $this->env->flag = true;

            return [$filename, $encoding];
        }

        return ['File not found', 404, null, 'ASCII'];
    }

    public function context(mixed ...$args): void
    {
        $this->context = $args;
    }

    public function render_template(
        string      $name,
        array|null  $context = null,
        int|null    $code = null,
        null|string $mimetype = null,
        null|string $encoding = null): array
    {
        $filename = $this->env->template . $name;

        if (is_file($filename)) {
            if (null === $context)
                $context = $this->context;

            else
                null === $this->context || $context = array_merge($this->context, $context);

            Http::buffer($filename, $context);

            return [template(), $code, $mimetype ?? 'text/html', $encoding ?? $this->encoding];
        }

        return ['Template not found', 500, null, 'ASCII'];
    }
}