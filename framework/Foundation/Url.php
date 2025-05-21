<?php

namespace Framework\Foundation;

class Url extends Routing\Mapper
{
    public function scheme(): string
    {
        return parent::ssl() ? 'https' : 'http';
    }

    public function host(): string
    {
        return $_SERVER['HTTP_HOST'];
    }

    public function path(): string
    {
        return parent::$url[0];
    }

    public function query(): string
    {
        return parent::$url[1] ?? '';
    }
}