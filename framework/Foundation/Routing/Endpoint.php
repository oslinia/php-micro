<?php

namespace Framework\Foundation\Routing;

class Endpoint extends Mapper
{
    private string $name;

    public function __construct(string $name, string $class, null|string $method)
    {
        parent::$endpoint[$name] = [$class, $method ?? '__invoke', array()];

        $this->name = $name;
    }

    public function middleware(mixed ...$args): void
    {
        parent::$endpoint[$this->name][2] = $args;
    }
}