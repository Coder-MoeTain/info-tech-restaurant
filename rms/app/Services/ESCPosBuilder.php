<?php

namespace App\Services;

/**
 * Minimal ESC/POS command builder for 80mm printers.
 */
class ESCPosBuilder
{
    protected string $buffer = '';

    public function text(string $text): self
    {
        $this->buffer .= $text . "\n";
        return $this;
    }

    public function line(): self
    {
        return $this->text(str_repeat('-', 32));
    }

    public function center(): self
    {
        $this->buffer .= "\x1b\x61\x01"; // center
        return $this;
    }

    public function left(): self
    {
        $this->buffer .= "\x1b\x61\x00";
        return $this;
    }

    public function feed(int $lines = 1): self
    {
        $this->buffer .= str_repeat("\n", $lines);
        return $this;
    }

    public function cut(): self
    {
        $this->buffer .= "\x1d\x56\x00";
        return $this;
    }

    public function bytes(): string
    {
        return $this->buffer;
    }
}
