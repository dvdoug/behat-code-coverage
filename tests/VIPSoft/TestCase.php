<?php
/**
 * Base Test Case
 *
 * @copyright 2013 Anthon Pang
 * @license BSD-3-Clause
 */

namespace VIPSoft;

use PHPUnit\Framework\TestCase as BaseTestCase;

/**
 * Test case
 *
 * @author Anthon Pang <apang@softwaredevelopment.ca>
 */
class TestCase extends BaseTestCase
{
    /**
     * @var array
     */
    static public $proxiedFunctions;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        if ( ! class_exists('\\VIPSoft\\Test\\FunctionProxy')) {
            eval(<<<END_OF_CLASS_MOCK
namespace VIPSoft\\Test {
    class FunctionProxy {
        public function invokeFunction()
        {
        }
    }
}
END_OF_CLASS_MOCK
            );
        }

        self::$proxiedFunctions = array();
    }

    /**
     * Mock a function
     *
     * @param string $functionName Name of function to be mocked
     * @param mixed  $function     A scalar, callable, or FunctionProxy mock
     * @param string $namespace    Optional namespace
     *
     * @return mixed
     */
    public function getMockFunction($functionName, $function = null, $namespace = null)
    {
        // eval() protection
        if ( ! preg_match('/^[A-Za-z0-9_]+$/D', $functionName)
            || ($namespace && ! preg_match('/^[A-Za-z0-9_]+$/D', $namespace))
        ) {
            throw new \Exception('Invalid function name and/or namespace');
        }

        // namespace guesser
        if ($namespace === null) {
            $caller = $this->getCaller();

            if ( ! $caller || ! isset($caller[0]) || ($pos = strrpos($caller[0], '\\')) === false) {
                throw new \Exception('Unable to mock functions in the root namespace');
            }

            $namespace = str_replace(array('\\Test\\', '\\Tests\\'), '\\', substr($caller[0], 0, $pos));
        }

        if ( ! function_exists('\\' . $namespace . '\\' . $functionName)) {
            eval(<<<END_OF_FUNCTION_MOCK
namespace $namespace {
    function $functionName()
    {
        return call_user_func_array(
            array('\VIPSoft\TestCase', 'invokeFunction'),
            array('$functionName', func_get_args())
        );
    }
}
END_OF_FUNCTION_MOCK
            );
        }

        if (is_null($function) || is_scalar($function)) {
            $function = function () use ($function) {
                return $function;
            };
        } elseif (is_object($function) && preg_match('/^Mock_FunctionProxy_[0-9a-f]+$/', get_class($function))) {
            $function = array($function, 'invokeFunction');
        }

        self::$proxiedFunctions[$functionName] = $function;
    }

    /**
     * Invoke function
     *
     * @return mixed
     */
    public static function invokeFunction()
    {
        $args = func_get_args();
        $functionName = array_shift($args);

        $callable = isset(self::$proxiedFunctions[$functionName])
            ? self::$proxiedFunctions[$functionName]
            : $functionName;

        return call_user_func_array($callable, $args[0]);
    }

    /**
     * Get caller
     *
     * @return array|null
     */
    private function getCaller()
    {
        $trace = debug_backtrace();

        // the first two lines in the call stack are getCaller and getMockFunction
        if (isset($trace[2])) {
            $class    = isset($trace[2]['class']) ? $trace[2]['class'] : null;
            $function = isset($trace[2]['function']) ? $trace[2]['function'] : null;

            return array($class, $function);
        }
    }
}
