<?php

namespace Ollama;

use Ai\FunctionCall;

class OllamaMessage implements \JsonSerializable
{
    /**
     * @var \Ai\FunctionCall[]
     */
    protected(set) array $function_calls = [];

    public function __construct(
        protected(set) string $role = 'user',
        protected(set) string $content = '',
        FunctionCall ...$function_calls,
    )
    {
        $this->function_calls = $function_calls;
    }

    public function jsonSerialize() : mixed
    {
        $data = [
            'role'    => $this->role,
            'content' => $this->content,
        ];
        if (count($this->function_calls) > 0)
        {
            $data['function_calls'] = $this->function_calls;
        }
        return $data;
    }
}
