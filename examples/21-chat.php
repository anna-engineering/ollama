<?php

require __DIR__ . '/../vendor/autoload.php';

$tools = [
    new \Qalam\FunctionCalling\User\GetLocation(),
    new \Qalam\FunctionCalling\User\GetWeather(),
];

$ollama = new Ollama\Ollama('http://localhost:11434', 'mistral-nemo:12b',);

$chat = $ollama->createChat(null, ...$tools);

$chat->message("Vous êtes un assistant dont la mission est de répondre aux questions de l’utilisateur de manière claire, précise et concise.", "system");
$chat->message("Bonjour, comment puis-je vous aider aujourd'hui ?", "assistant");
$chat->message('Quel est la meteo ?');

$chat->exec();

$chat->dataInfo();


