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
     * Constructor.
     *
     * [
     *     'baseUrl' => 'http://api.example.com/1.0/coverage',
     *     'auth'    => [
     *                      'user'     => 'user name',
     *                      'password' => 'password',
     *                  ],
     *     'start'   => [
     *                      'method' => 'POST',
     *                      'path'   => '/',
     *                  ],
     *     'fetch'   => [
     *                      'method' => 'GET',
     *                      'path'   => '/',
     *                  ],
     *     'stop'    => [
     *                      'method' => 'DELETE',
     *                      'path'   => '/',
     *                  ],
     * ]
     *
     * @param array  $config          Configuration
     * @param string $clientClassName HTTP client class name
     */
    public function __construct(array $config, $clientClassName = '\Guzzle\Http\Client')
    {
        $this->config          = $config;
        $this->clientClassName = $clientClassName;
    }

    /**
     * Start collection of code coverage information.
     */
    public function start()
    {
        $client = new $this->clientClassName($this->config['baseUrl']);

        $request = $this->buildRequest($client, 'start');

        $response = $request->send();

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('remote driver start failed: ' . $response->getReasonPhrase());
        }
    }

    /**
     * Stop collection of code coverage information.
     *
     * @return array
     */
    public function stop()
    {
        $client = new $this->clientClassName($this->config['baseUrl']);

        $request = $this->buildRequest($client, 'fetch');
        $request->setHeader('Accept', 'application/json');

        $response = $request->send();

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('remote driver fetch failed: ' . $response->getReasonPhrase());
        }

        $request = $this->buildRequest($client, 'stop');
        $request->send();

        return json_decode($response->getBody(true));
    }

    /**
     * Construct request
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
