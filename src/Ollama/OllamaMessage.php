<?php

namespace Ollama;

use Ai\FunctionCall;

class OllamaMessage implements \JsonSerializable
{
    /**
     * @var \Ai\FunctionCall[]
     */
    protected(set) array $tool_calls = [];

    protected(set) array $extra = [];

    public function __construct(
        protected(set) string $role = 'user',
        protected(set) string $content = '',
        FunctionCall ...$tool_calls,
    )
    {
        $this->tool_calls = $tool_calls;
    }

    public function setExtra(string $name, mixed $value) : static
    {
        $this->extra[$name] = $value;

        return $this;
    }

    public function jsonSerialize() : mixed
    {
        return [
            'role'    => $this->role,
            'content' => $this->content,
            ...$this->extra,
            ...(count($this->tool_calls) ? ['tool_calls' => $this->tool_calls] : []),
        ];
    }
}
