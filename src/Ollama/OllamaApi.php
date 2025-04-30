<?php

namespace Ollama;

use Ollama\Resource\VersionResource;
use A\Async\PromiseProxyInterface;

/**
 * Cette classe gère l’ensemble des appels possibles à l’API Ollama.
 */
class OllamaApi
{
    public function __construct(protected Ollama $ollama)
    {
    }

    /**
     * Lance un prompt simple de génération (POST /generate)
     *
     * @param string $prompt  Contenu du prompt
     * @param array  $options Paramètres additionnels
     */
    public function generate(string $prompt, array $options = []) : PromiseProxyInterface
    {
        $payload = [
            'model'  => $this->ollama->model,
            'prompt' => $prompt,
            'stream' => false,
            //'system' => "Tu es un assistant qui ne répond qu'aux questions en rapport avec l'informatique et le codage ! Si l'utilisateur pose une question qui n'est pas une question informatique alors tu dois lui répondre que tu n'es pas autorisé à répondre à ça et l'inviter à poser une question sur l'informatique ou du code",
        ];

        return fetch_json($this->ollama->base_url . '/api/generate', method: 'POST', data: $payload);
    }

    /**
     * Generate Embeddings
     *
     * Generate embeddings from a model
     *
     * @param string $input text or list of text to generate embeddings for
     * @param bool $truncate truncates the end of each input to fit within context length. Returns error if false and context length is exceeded. Defaults to true
     * @param array $options additional model parameters listed in the documentation for the Modelfile such as temperature
     * @param string $keep_alive controls how long the model will stay loaded into memory following the request (default: 5m)
     * @param string|null $model name of model to generate embeddings from
     * @return PromiseProxyInterface
     */
    public function embed(string $input, bool $truncate = true, array $options = [], string $keep_alive = '5m', ?string $model = null) : PromiseProxyInterface
    {
        $payload = [
            'model'  => $model ?? $this->ollama->model,
            'input' => $input,
            'truncate' => $truncate,
            ...(count($options) ? ['options' => $options] : []),
            'keep_alive' => $keep_alive,
        ];

        return fetch_json($this->ollama->base_url . '/api/embed', method: 'POST', data: $payload);
    }

    /**
     * GET /version
     *
     * @return PromiseProxyInterface|VersionResource
     */
    public function getVersion() : PromiseProxyInterface
    {
        return fetch_json($this->ollama->base_url . '/api/version');
    }
}
