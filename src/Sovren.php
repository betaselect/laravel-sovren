<?php

namespace Inwave\LaravelSovren;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class Sovren
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function getAccount()
    {
        $response = $this->client->get('account')->getBody()->getContents();
        return json_decode($response, true);
    }

    public function parse(string $resume)
    {
        try {
            $response = $this->client->post('parser/resume', [
                'json' => [
                    'DocumentAsBase64String' => base64_encode($resume)
                ]
            ]);
        } catch (ClientException $e) {
            return json_decode($e->getResponse()->getBody(), true);
        }

        return json_decode($response->getBody()->getContents(), true);
    }
}
