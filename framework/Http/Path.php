<?php

namespace Framework\Http;

use stdClass;

class Path extends stdClass
{
    private array $tokens;

    public function __construct(array $tokens)
    {
        $this->tokens = $tokens;
    }

    public function __get(string $name): string
    {
        return $this->tokens[$name];
    }

    public function __isset(string $name): bool
    {
        return array_key_exists($name, $this->tokens);
    }
}