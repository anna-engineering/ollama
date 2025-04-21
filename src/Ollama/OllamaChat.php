<?php

namespace Ollama;

use Ai\FunctionCallingInterface;

/**
 * Gère une conversation (prompt chaining), en stockant les messages échangés.
 */
class OllamaChat implements \JsonSerializable
{
    protected string $model;

    /**
     * @var OllamaMessage[]
     */
    protected array $messages = [];

    /**
     * @var FunctionCallingInterface[]
     */
    protected array $tools = [];

    public function __construct(
        protected Ollama $ollama,
        string|null $model = null,
        FunctionCallingInterface ...$tools
    )
    {
        $this->model = $model ?? $ollama->model;
        $this->tools = $tools;
    }

    /**
     * Ajoute un message côté “user”.
     */
    public function message(string $content, string $role = 'user')// : ChatResponseResource
    {
        $this->messages[] = new OllamaMessage($role, $content);
    }

    public function jsonSerialize() : mixed
    {
        return [
            'model'    => $this->model,
            'messages' => $this->messages,
            'tools'    => $this->tools,
        ];
    }

    public function data()
    {
        return json_encode($this, JSON_PRETTY_PRINT) . PHP_EOL;
    }

    public function dataInfo()
    {
        echo $this->data();
    }

    public function exec()
    {
        $json = $this->jsonSerialize();
        $json['stream'] = false;
        $data = fetch_json($this->ollama->base_url . '/api/chat', method: 'POST', data: $json);

        $tool_calls = [];
        foreach ($data->message->tool_calls ?? [] as $tool_call)
        {
            $tool_calls[] = new \Ai\FunctionCall($tool_call->function->name, (array) $tool_call->function->arguments);
        }

        $this->messages[] = new OllamaMessage($data->message->role, $data->message->content, ...$tool_calls);
    }
}
