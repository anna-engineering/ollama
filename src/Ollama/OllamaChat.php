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
    protected(set) array $tools = [];

    public string $format = '';
    public array $options = [];
    public bool $stream = false;
    public string $keep_alive = '';

    public function __construct(
        protected Ollama $ollama,
        string|null $model = null,
        FunctionCallingInterface ...$tools
    )
    {
        $this->model = $model ?? $ollama->model;

        foreach ($tools as $tool)
        {
            $this->tools[$tool->getName()] = $tool;
        }
    }

    public function addTools(FunctionCallingInterface ...$tools)
    {
        foreach ($tools as $tool)
        {
            $this->tools[$tool->getName()] = $tool;
        }
    }

    public function removeTools(FunctionCallingInterface ...$tools)
    {
        foreach ($tools as $tool)
        {
            unset($this->tools[$tool->getName()]);
        }
    }

    public function message(string $content, string $role = 'user') : OllamaMessage
    {
        return $this->messages[] = new OllamaMessage($role, $content);
    }

    public function systemMessage(string $content) : OllamaMessage
    {
        return $this->message($content, 'system');
    }

    public function assistantMessage(string $content) : OllamaMessage
    {
        return $this->message($content, 'assistant');
    }

    public function toolMessage(string $content, string $name) : OllamaMessage
    {
        return $this->message($content, 'tool')->setExtra('name', $name);
    }

    public function userMessage(string $content) : OllamaMessage
    {
        return $this->message($content, 'user');
    }

    public function getContext()
    {
        return $this->jsonSerialize();
    }

    public function prompt() : OllamaMessage
    {
        $data = fetch_json($this->ollama->base_url . '/api/chat', method: 'POST', data: $this->getContext());

        $tool_calls = [];
        foreach ($data->message->tool_calls ?? [] as $tool_call)
        {
            $tool_calls[] = new \Ai\FunctionCall($tool_call->function->name, (array) $tool_call->function->arguments);
        }

        $data->__isset('message');

        $message = new OllamaMessage($data->message->role, $data->message->content, ...$tool_calls);

        //$this->messages[]= $message;

        return $message;
    }

    public function addMessage(OllamaMessage $message) : static
    {
        $this->messages[] = $message;

        return $this;
    }

    protected function push()
    {
        $payload = $this->jsonSerialize();
        $payload['stream'] = false;

        $data = fetch_json($this->ollama->base_url . '/api/chat', method: 'POST', data: $payload);

        $tool_calls = [];
        foreach ($data->message->tool_calls ?? [] as $tool_call)
        {
            $tool_calls[] = new \Ai\FunctionCall($tool_call->function->name, (array) $tool_call->function->arguments);
        }

        $this->messages[] = new OllamaMessage($data->message->role, $data->message->content, ...$tool_calls);

        foreach ($tool_calls as $tool_call)
        {
            $result = $this->tools[$tool_call->name]?->__invok($tool_call->arguments);
            $result_str = json_encode($result);

            $this->toolMessage($result_str, $tool_call->name);
        }
    }






    public function promptxxx($content)
    {
        $this->message($content, 'user');

        /*
        $payload = $this->jsonSerialize();
        $payload['stream'] = false;
        $result = fetch_json($this->ollama->base_url . '/api/chat', method: 'POST', data: $payload);

        $tool_calls = [];
        foreach ($data->message->tool_calls ?? [] as $tool_call)
        {
            $tool_calls[] = new \Ai\FunctionCall($tool_call->function->name, (array) $tool_call->function->arguments);
        }

        $this->messages[] = new OllamaMessage($data->message->role, $data->message->content, ...$tool_calls);
        */
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

    public function jsonSerialize() : mixed
    {
        return [
            'model'    => $this->model,
            'messages' => $this->messages,
            'tools'    => array_values($this->tools),
            ...(empty($this->format) ? [] : ['format' => $this->format]),
            ...(empty($this->options) ? [] : ['options' => $this->options]),
            ...['stream' => $this->stream],
            ...(empty($this->keep_alive) ? [] : ['keep_alive' => $this->keep_alive]),
        ];
    }

    public function debug()
    {
        echo json_encode($this->getContext(), JSON_PRETTY_PRINT) . PHP_EOL;
    }


    public function setOption(string $name, string $value) : static
    {
        $this->options[$name] = $value;

        return $this;
    }
}
