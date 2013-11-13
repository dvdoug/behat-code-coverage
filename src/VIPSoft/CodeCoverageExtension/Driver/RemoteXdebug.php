<?php
/**
 * Code Coverage Driver
 *
 * @copyright 2013 Anthon Pang
 * @license BSD-2-Clause
 */

namespace VIPSoft\CodeCoverageExtension\Driver;

use Guzzle\Http\Client;

/**
 * Remote xdebug driver
 *
 * @author Anthon Pang <apang@softwaredevelopment.ca>
 */
class RemoteXdebug implements \PHP_CodeCoverage_Driver
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var \Guzzle\Http\Client
     */
    private $client;

    /**
     * Constructor
     *
     * [
     *     'base_url' => 'http://api.example.com/1.0/coverage',
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
     * @param array               $config Configuration
     * @param \Guzzle\Http\Client $client HTTP client
     */
    public function __construct(array $config, Client $client)
    {
        $this->config = $config;

        $this->client = $client;
        $this->client->setBaseUrl($config['base_url']);
    }

    /**
     * {@inheritdoc}
     */
    public function start()
    {
        $request = $this->buildRequest('create');

        $response = $request->send();

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('remote driver start failed: ' . $response->getReasonPhrase());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function stop()
    {
        $request = $this->buildRequest('read');
        $request->setHeader('Accept', 'application/json');

        $response = $request->send();

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('remote driver fetch failed: ' . $response->getReasonPhrase());
        }

        $request = $this->buildRequest('delete');
        $request->send();

        return json_decode($response->getBody(true), true);
    }

    /**
     * Construct request
     *
     * @param string $endpoint
     *
     * @return \Guzzle\Http\Message\Request
     */
    private function buildRequest($endpoint)
    {
        $method = strtolower($this->config[$endpoint]['method']);

        if ( ! in_array($method, array('get', 'post', 'put', 'delete'))) {
            throw new \Exception($endpoint . ' method must be GET, POST, PUT, or DELETE');
        }

        $request = $this->client->$method($this->config[$endpoint]['path']);

        if (isset($this->config['auth'])) {
            $request->setAuth($this->config['auth']['user'], $this->config['auth']['password']);
        }

        return $request;
    }
}
