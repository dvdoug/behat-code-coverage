<?php
/**
 * Code Coverage Driver
 *
 * @copyright 2013 Anthon Pang
 * @license BSD-2-Clause
 */

namespace VIPSoft\CodeCoverageExtension\Driver;

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
     * @var string
     */
    private $clientClassName;

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
     * @param array  $config          Configuration
     * @param string $clientClassName HTTP client class name
     */
    public function __construct(/*array*/ $config, $clientClassName = '\Guzzle\Http\Client')
    {
        $this->config          = $config;
        $this->clientClassName = $clientClassName;
    }

    /**
     * {@inheritdoc}
     */
    public function start()
    {
        $client = new $this->clientClassName($this->config['base_url']);

        $request = $this->buildRequest($client, 'create');

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
        $client = new $this->clientClassName($this->config['base_url']);

        $request = $this->buildRequest($client, 'read');
        $request->setHeader('Accept', 'application/json');

        $response = $request->send();

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('remote driver fetch failed: ' . $response->getReasonPhrase());
        }

        $request = $this->buildRequest($client, 'delete');
        $request->send();

        return json_decode($response->getBody(true), true);
    }

    /**
     * Construct request
     *
     * @param \Guzzle\Http\Client $client
     * @param string              $endpoint
     *
     * @return \Guzzle\Http\Message\Request
     */
    private function buildRequest($client, $endpoint)
    {
        $method = strtolower($this->config[$endpoint]['method']);

        if ( ! in_array($method, array('get', 'post', 'put', 'delete'))) {
            throw new \Exception($endpoint . ' method must be GET, POST, PUT, or DELETE');
        }

        $request = $client->$method($this->config[$endpoint]['path']);

        if (isset($this->config['auth'])) {
            $request->setAuth($this->config['auth']['user'], $this->config['auth']['password']);
        }

        return $request;
    }
}
