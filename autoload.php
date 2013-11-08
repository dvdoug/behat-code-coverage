<?php
/**
 * @copyright 2013 Anthon Pang
 * @license BSD-2-Clause
 */

namespace VIPSoft;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/tests/VIPSoft/TestCase.php';

/**
 * Generic autoloader
 *
 * @author Anthon Pang <apang@softwaredevelopment.ca>
 */
class Bootstrap
{
    /**
     * Load class
     *
     * @param string $class Class name
     */
    public static function autoload($class)
    {
        $file = str_replace(array('\\', '_'), '/', $class);
        $path = __DIR__ . '/src/' . $file . '.php';

        if (file_exists($path)) {
            include_once $path;
        }
    }
}

spl_autoload_register('VIPSoft\Bootstrap::autoload');
