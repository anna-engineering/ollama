<?php

namespace Ollama;

use Ollama\OllamaApi;

class Ollama
{
    protected(set) OllamaApi $api;

    public function __construct(
        protected(set) string $base_url = 'http://localhost:11434',
        protected(set) string $model = 'llama3.2'
    )
    {
        $this->api = new OllamaApi($this);
    }

    /**
     * Crée un nouvel objet de type OllamaChat
     *
     * @param string|null $model  Modèle à utiliser (sinon on prend celui passé au constructeur)
     * @param string      ...$tools Liste des noms de classes (tools) que l’on souhaite utiliser
     */
    public function createChat(?string $model = null, string ...$tools) : OllamaChat
    {
        return new OllamaChat($this, $model ?? $this->model, ...$tools);
    }
}
