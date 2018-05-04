<?php
/**
 * Code Coverage Driver
 *
 * @copyright 2013 Anthon Pang
 *
 * @license BSD-2-Clause
 */

namespace LeanPHP\Behat\CodeCoverage\Driver;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use SebastianBergmann\CodeCoverage\Driver\Driver as DriverInterface;

/**
 * Remote xdebug driver
 *
 * @author Anthon Pang <apang@softwaredevelopment.ca>
 */
class RemoteXdebug implements DriverInterface
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
     * Constructor
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

    /**
     * {@inheritdoc}
     */
    public function start(bool $determineUnusedAndDead = true): void
    {
        $response = $this->sendRequest('create');

        if ($response->getStatusCode() !== 200) {
            throw new \Exception(sprintf('remote driver start failed: %s', $response->getReasonPhrase()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function stop(): array
    {
        $response = $this->sendRequest('read', ['headers' => ['Accept' => 'application/json']]);

        if ($response->getStatusCode() !== 200) {
            throw new \Exception(sprintf('remote driver fetch failed: %s', $response->getReasonPhrase()));
        }

        $response = $this->sendRequest('delete');

        return json_decode($response->getBody(true), true);
    }

    /**
     * Construct request
     *
     * @param string $endpoint
     * @param array  $headers
     *
     * @return GuzzleHttp\Psr7\Response
     */
    private function sendRequest($endpoint, $headers = array())
    {
        $method = strtolower($this->config[$endpoint]['method']);

        if (! in_array($method, array('get', 'post', 'put', 'delete'))) {
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
}
