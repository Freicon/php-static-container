<?php

namespace Freicon\StaticContainer;

use Pimple\ServiceProviderInterface;

/**
 * Container main class.
 */
class Container extends \Pimple\Container
{
    /**
     * @var \Pimple\Container
     */
    private static $container;

    /**
     * @var \Pimple\Container
     */
    private static $outerContainer;

    private static $commands = array();

    /**
     * @param \Pimple\Container $container
     */
    public static function setContainer(\Pimple\Container $container)
    {
        self::$container = $container;
        foreach (self::$commands as $command) {
            call_user_func_array(array($container, $command['command']), $command['args']);
        }
    }

    /**
     * Returns the current registered Container Instance
     * @return \Pimple\Container
     */
    public static function instance()
    {
        if (!self::$outerContainer) {
            self::$outerContainer = new static();
        }
        if (!self::$container) {
            self::$container = new \Pimple\Container();
        }
        return self::$outerContainer;
    }

    /**
     * @param $command
     * @param $args
     */
    private function registerCommand($command, $args)
    {
        self::$commands[] = array('command' => $command, 'args' => $args);
    }

    public function offsetSet($id, $value)
    {
        $this->registerCommand('offsetSet', array($id, $value));
        self::$container->offsetSet($id, $value);
    }

    public function offsetGet($id)
    {
        return self::$container->offsetGet($id);
    }

    public function offsetExists($id)
    {
        return self::$container->offsetExists($id);
    }

    public function offsetUnset($id)
    {
        $this->registerCommand('offsetUnset', array($id));
        self::$container->offsetUnset($id);
    }

    public function factory($callable)
    {
        $this->registerCommand('factory', array($callable));
        return self::$container->factory($callable);
    }

    public function protect($callable)
    {
        $this->registerCommand('protect', array($callable));
        return self::$container->protect($callable);
    }

    public function raw($id)
    {
        return self::$container->raw($id);
    }

    public function extend($id, $callable)
    {
        $this->registerCommand('extend', array($id, $callable));
        return self::$container->extend($id, $callable);
    }

    public function keys()
    {
        return self::$container->keys();
    }

    public function register(ServiceProviderInterface $provider, array $values = array())
    {
        $this->registerCommand('register', array($provider, $values));
        return self::$container->register($provider, $values);
    }

}