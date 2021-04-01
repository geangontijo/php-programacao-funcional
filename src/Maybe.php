<?php

namespace PHPFuncional;

class Maybe
{
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public static function of($value): self
    {
        return new Maybe($value);
    }

    public function isNothing(): bool
    {
        return !is_array($this->value);
    }

    public function map(callable $fn): self
    {
        if ($this->isNothing()) return Maybe::of($this->value);

        return Maybe::of($fn($this->value));
    }

    public function getOrElse(): ?array
    {
        return $this->isNothing() ? [] : $this->value;
    }
}