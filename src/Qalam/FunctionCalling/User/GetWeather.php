<?php

namespace Qalam\FunctionCalling\User;

use Ai\FunctionCalling;
use Ai\FunctionCallingInterface;
use Ai\FunctionCallingParameter;

class GetWeather extends FunctionCalling implements FunctionCallingInterface
{
    public function __construct()
    {
        parent::__construct(
            'get_weather',
            'Get current temperature for a given location.',
            new FunctionCallingParameter(
                name: 'location',
                type: \Ai\FunctionCallingParameterType::STRING,
                description: 'City and country e.g. `Bogotá, Colombia`',
                required: true,
            ),
            new FunctionCallingParameter(
                name: 'unit',
                type: \Ai\FunctionCallingParameterType::STRING,
                description: 'Unit of temperature, e.g. `celsius` or `fahrenheit` (default: `celsius`)',
                values: ['celsius', 'fahrenheit'],
            ),
        );
    }

    public function __invoke($location, $unit = 'celsius')
    {
        if (!str_contains(strtolower($location), 'toulouse'))
        {
            return [
                'location' => $location,
                'unit'     => $unit,
                'temperature' => 19,
                'description' => 'Sunny',
            ];
        }

        return [
            'location' => $location,
            'unit'     => $unit,
            'temperature' => 20,
            'description' => 'Sunny',
        ];
    }
}