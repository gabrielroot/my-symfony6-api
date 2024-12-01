<?php

namespace App\Integration\ViaCep;

use App\Exception\Integration\ViaCep\BadCepException;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class ViaCepIntegration
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://viacep.com.br/ws/',
            'timeout'  => 5.0,
        ]);
    }

    public function getZipCode(string $cep)
    {
        $response = $this->client->request('GET', "$cep/json", ['http_errors' => false]);
        return $this->handleResponse($response);
    }

    private function handleResponse(ResponseInterface $response)
    {
        if ($response->getStatusCode() !== 200) {
            throw new BadCepException();
        }

        $data = json_decode($response->getBody()->getContents(), true);

        if (array_key_exists('error', $data)) {
            throw new BadCepException();
        }

        return $data;
    }
}