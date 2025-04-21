<?php

require __DIR__ . '/../vendor/autoload.php';

$ollama = new Ollama\Ollama('http://localhost:11434');

echo $ollama->api->getVersion()->version . PHP_EOL;

