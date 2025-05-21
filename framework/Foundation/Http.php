<?php

namespace Framework\Foundation;

use stdClass;

function template(): string
{
    extract(Http::$buffer->extract);

    ob_start();

    require Http::$buffer->filename;

    return ob_get_clean();
}

class Http extends Routing\Mapper
{
    public static object $buffer;
    public static stdClass $env;

    private function status(int $code): void
    {
        if (ob_get_level())
            ob_end_clean();

        header($_SERVER["SERVER_PROTOCOL"] . [
                200 => ' 200 OK',
                301 => ' 301 Moved Permanently',
                302 => ' 302 Moved Temporarily',
                307 => ' 307 Temporary Redirect',
                308 => ' 308 Permanent Redirect',
                404 => ' 404 Not Found',
                500 => ' 500 Internal Server Error',
            ][$code]);
    }

    private function content(int $length, string $mimetype, null|string $encoding): void
    {
        if (str_starts_with($mimetype, 'text/'))
            $mimetype .= '; charset=' . ($encoding ?? 'UTF-8');

        header('content-length: ' . $length);
        header('content-type: ' . $mimetype);
    }

    private function media(string $filename, null|string $encoding): void
    {
        $mime = fn(string $filename) => match (pathinfo($filename, PATHINFO_EXTENSION)) {
            'css' => 'text/css',
            'htm', 'html' => 'text/html',
            'txt' => 'text/plain',
            'xml' => 'text/xml',
            'gif' => 'image/gif',
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'svg' => 'image/svg+xml',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'pdf' => 'application/pdf',
            default => 'application/octet-stream'
        };

        $this->status(200);

        $this->content(filesize($filename), $mime($filename), $encoding);

        if ($f = fopen($filename, 'rb')) {
            while (!feof($f))
                echo fread($f, 1024);

            fclose($f);
        }
    }

    private function document(
        string      $body,
        int|null    $code = null,
        null|string $mimetype = null,
        null|string $encoding = null): void
    {
        $this->status($code ?? 200);

        $this->content(strlen($body), $mimetype ?? 'text/plain', $encoding);

        echo $body;
    }

    public function __construct(string $routing)
    {
        $this->routing = $routing . DIRECTORY_SEPARATOR;

        parent::$flag || parent::caching($routing);

        $response = parent::response();

        if (is_string($response))
            $this->document($response);

        elseif (is_array($response) and is_string($response[0]))
            self::$env->flag ? $this->media(...$response) : $this->document(...$response);

        else
            $this->document('Invalid data for response', 500, encoding: 'ASCII');
    }

    private static function environment(string $resource, string $src): void
    {
        self::$env = new stdClass;
        self::$env->flag = false;
        self::$env->media = $resource . 'media' . DIRECTORY_SEPARATOR;
        self::$env->template = $src . 'template' . DIRECTORY_SEPARATOR;

        foreach (require $resource . 'config.php' as $key => $value)
            self::$env->$key = $value;
    }

    public static function request(string $dirname): void
    {
        self::environment(
            $resource = $dirname . DIRECTORY_SEPARATOR . 'resource' . DIRECTORY_SEPARATOR,
            $src = $dirname . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR,
        );

        parent::$flag = is_dir($routing = $resource . 'routing');
        parent::$flag || parent::$tmp = array();

        require $src . 'app' . DIRECTORY_SEPARATOR . 'mapper.php';

        new static($routing);
    }

    public static function build(array $args): null|string
    {
        $name = array_shift($args);

        if (isset(parent::$urls[$name])) {
            $link = parent::$urls[$name];

            $size = count($args);

            if (isset($link[$size])) {
                [$path, $pattern] = $link[$size];

                foreach ($args as $mask => $value)
                    $path = str_replace('{' . $mask . '}', $value, $path);

                if (preg_match($pattern, $path, $matches))
                    return $matches[0];
            }
        }

        return null;
    }

    public static function buffer(string $filename, array|null $context): void
    {
        $lang = ['lang' => self::$env->lang];

        self::$buffer = (object)[
            'filename' => $filename,
            'extract' => is_null($context) ? $lang : array_merge($lang, $context),
        ];
    }
}