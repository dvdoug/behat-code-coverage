<?php

declare(strict_types=1);
/**
 * Code Coverage Driver.
 *
 * @copyright 2013 Anthon Pang
 *
 * @license BSD-2-Clause
 */

namespace DVDoug\Behat\CodeCoverage\Driver;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use SebastianBergmann\CodeCoverage\Driver\Driver;
use SebastianBergmann\CodeCoverage\RawCodeCoverageData;

/**
 * Remote xdebug driver.
 *
 * @author Anthon Pang <apang@softwaredevelopment.ca>
 */
class RemoteXdebug extends Driver
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var GuzzleHttp\Client
     */
    private $client;

    /**
     * Constructor.
     *
     * [
     *     'base_uri' => 'http://api.example.com/1.0/coverage',
     *     'auth'     => [
     *                       'user'     => 'user name',
     *                       'password' => 'password',
     *                   ],
     *     'create'   => [
     *                       'method' => 'POST',
     *                       'path'   => '/',
     *                   ],
     *     'read'     => [
     *                       'method' => 'GET',
     *                       'path'   => '/',
     *                   ],
     *     'delete'   => [
     *                       'method' => 'DELETE',
     *                       'path'   => '/',
     *                   ],
     * ]
     *
     * @param array             $config Configuration
     * @param GuzzleHttp\Client $client HTTP client
     */
    public function __construct(array $config, Client $client)
    {
        $this->config = $config;

        $this->client = $client;
        //$this->client->setBaseUrl($config['base_url']);
    }

    public function start(bool $determineUnusedAndDead = true): void
    {
        $response = $this->sendRequest('create');

        if ($response->getStatusCode() !== 200) {
            throw new \Exception(sprintf('remote driver start failed: %s', $response->getReasonPhrase()));
        }
    }

    public function stop(): RawCodeCoverageData
    {
        $response = $this->sendRequest('read', ['headers' => ['Accept' => 'application/json']]);

        if ($response->getStatusCode() !== 200) {
            throw new \Exception(sprintf('remote driver fetch failed: %s', $response->getReasonPhrase()));
        }

        $this->sendRequest('delete');

        return RawCodeCoverageData::fromXdebugWithoutPathCoverage(json_decode($response->getBody(true), true));
    }

    private function sendRequest(string $endpoint, array $headers = []): ResponseInterface
    {
        $method = strtolower($this->config[$endpoint]['method']);

        if (!in_array($method, ['get', 'post', 'put', 'delete'])) {
            throw new \Exception(sprintf('%s method must be GET, POST, PUT, or DELETE', $endpoint));
        }

        if (isset($this->config['auth'])) {
            $response = $this->client->request(
                $method,
                $this->config[$endpoint]['path'],
                [
                    'auth' => [$this->config['auth']['user'], $this->config['auth']['password']],
                    'headers' => $headers,
                ]
            );
        } else {
            $response = $this->client->request(
                $method,
                $this->config[$endpoint]['path'],
                [
                    'headers' => $headers,
                ]
            );
        }

        return $response;
    }

    public function name(): string
    {
        return 'Remote Xdebug';
    }
}
