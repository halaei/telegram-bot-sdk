<?php

namespace Telegram\Bot\Tests\Mocks;

use Telegram\Bot\Api;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Handler\MockHandler;
use Telegram\Bot\HttpClients\GuzzleHttpClient;

class Mocker
{
    /**
     * This creates a raw api response to simulate what Telegram replies
     * with.
     *
     * @param array $apiResponseFields
     * @param bool  $ok
     *
     * @return Api
     */
    public static function createApiResponse(array $apiResponseFields, $ok = true)
    {
        $response = [
            'ok'          => $ok,
            'description' => '',
            'result'      => $apiResponseFields,
        ];

        return self::setTelegramResponse($response);
    }

    /**
     * Recreates the Api object, using a mock http client, with predefined
     * responses containing the provided $body.
     *
     * @param $body
     *
     * @return Api
     */
    private static function setTelegramResponse($body)
    {
        $body = json_encode($body);
        $mock = new MockHandler([
            new Response(200, [], $body),
            new Response(200, [], $body),
            // two times because Api::commandsHandler makes two requests (when not using webhook method).
        ]);
        $handler = HandlerStack::create($mock);
        $client = new GuzzleHttpClient(new Client(['handler' => $handler]));

        return new Api('token', false, $client);
    }
}
