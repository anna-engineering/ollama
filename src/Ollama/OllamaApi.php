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
     * GET /version
     *
     * @return PromiseProxyInterface|VersionResource
     */
    public function getVersion() : PromiseProxyInterface
    {
        return fetch_json($this->ollama->base_url . '/api/version');
    }
}
