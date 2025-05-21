<?php

namespace Framework\Foundation\Routing;

class Rule extends Mapper
{
    private string $path;

    private function match(string $path, string $name): array
    {
        $this->path = $path;

        $names = $tokens = array();

        if (preg_match_all('/{([A-Za-z0-9_-]+)}/', $path, $matches)) {
            foreach ($matches[0] as $mask)
                $tokens[$mask] = '([A-Za-z0-9_-]+)';

            $names = $matches[1];
        }

        return 0 === ($size = count($names)) ? [$name, $size] : [$name, $size, $names, $tokens];
    }

    public function __construct(string $path, string $name)
    {
        parent::$flag || parent::$tmp[$path] = $this->match($path, $name);
    }

    public function where(string ...$args): void
    {
        if (!parent::$flag)
            foreach ($args as $name => $pattern)
                parent::$tmp[$this->path][3]['{' . $name . '}'] = '(' . $pattern . ')';
    }
}