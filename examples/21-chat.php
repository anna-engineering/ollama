<?php

require __DIR__ . '/../vendor/autoload.php';

$ollama = new Ollama\Ollama('http://localhost:11434', 'mistral-nemo:12b');

$chat = $ollama->createChat();

$chat->message("Vous êtes un assistant dont la mission est de répondre aux questions de l’utilisateur de manière claire, précise et concise.", "system");
$chat->message("Bonjour, comment puis-je vous aider aujourd'hui ?", "assistant");
$chat->message('Je veux que tu me racontes une blague.');

$chat->exec();

$chat->dataInfo();




