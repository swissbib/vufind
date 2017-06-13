<?php

namespace ProxyManagerGeneratedProxy\__PM__\VuFind\Auth\ILSAuthenticator;

class Generatedb8c05fd64cf2dad2e97dfb9fbfa7659a extends \VuFind\Auth\ILSAuthenticator implements \ProxyManager\Proxy\VirtualProxyInterface
{

    /**
     * @var \Closure|null initializer responsible for generating the wrapped object
     */
    private $valueHolder593faf8e03daf096421245 = null;

    /**
     * @var \Closure|null initializer responsible for generating the wrapped object
     */
    private $initializer593faf8e03e4c094214682 = null;

    /**
     * @var bool[] map of public properties of the parent class
     */
    private static $publicProperties593faf8e0390d551906800 = array(
        
    );

    private static $signatureb8c05fd64cf2dad2e97dfb9fbfa7659a = 'YTozOntzOjk6ImNsYXNzTmFtZSI7czoyODoiVnVGaW5kXEF1dGhcSUxTQXV0aGVudGljYXRvciI7czo3OiJmYWN0b3J5IjtzOjUwOiJQcm94eU1hbmFnZXJcRmFjdG9yeVxMYXp5TG9hZGluZ1ZhbHVlSG9sZGVyRmFjdG9yeSI7czoxOToicHJveHlNYW5hZ2VyVmVyc2lvbiI7czo1OiIxLjAuMCI7fQ==';

    /**
     * {@inheritDoc}
     */
    public function getStoredCatalogCredentials()
    {
        $this->initializer593faf8e03e4c094214682 && $this->initializer593faf8e03e4c094214682->__invoke($this->valueHolder593faf8e03daf096421245, $this, 'getStoredCatalogCredentials', array(), $this->initializer593faf8e03e4c094214682);

        return $this->valueHolder593faf8e03daf096421245->getStoredCatalogCredentials();
    }

    /**
     * {@inheritDoc}
     */
    public function storedCatalogLogin()
    {
        $this->initializer593faf8e03e4c094214682 && $this->initializer593faf8e03e4c094214682->__invoke($this->valueHolder593faf8e03daf096421245, $this, 'storedCatalogLogin', array(), $this->initializer593faf8e03e4c094214682);

        return $this->valueHolder593faf8e03daf096421245->storedCatalogLogin();
    }

    /**
     * {@inheritDoc}
     */
    public function newCatalogLogin($username, $password)
    {
        $this->initializer593faf8e03e4c094214682 && $this->initializer593faf8e03e4c094214682->__invoke($this->valueHolder593faf8e03daf096421245, $this, 'newCatalogLogin', array('username' => $username, 'password' => $password), $this->initializer593faf8e03e4c094214682);

        return $this->valueHolder593faf8e03daf096421245->newCatalogLogin($username, $password);
    }

    /**
     * @override constructor for lazy initialization
     *
     * @param \Closure|null $initializer
     */
    public function __construct($initializer)
    {
        $this->initializer593faf8e03e4c094214682 = $initializer;
    }

    /**
     * @param string $name
     */
    public function & __get($name)
    {
        $this->initializer593faf8e03e4c094214682 && $this->initializer593faf8e03e4c094214682->__invoke($this->valueHolder593faf8e03daf096421245, $this, '__get', array('name' => $name), $this->initializer593faf8e03e4c094214682);

        if (isset(self::$publicProperties593faf8e0390d551906800[$name])) {
            return $this->valueHolder593faf8e03daf096421245->$name;
        }

        $realInstanceReflection = new \ReflectionClass(get_parent_class($this));

        if (! $realInstanceReflection->hasProperty($name)) {
            $targetObject = $this->valueHolder593faf8e03daf096421245;

            $backtrace = debug_backtrace(false);
            trigger_error('Undefined property: ' . get_parent_class($this) . '::$' . $name . ' in ' . $backtrace[0]['file'] . ' on line ' . $backtrace[0]['line'], \E_USER_NOTICE);
            return $targetObject->$name;;
            return;
        }

        $targetObject = $this->valueHolder593faf8e03daf096421245;
        $accessor = function & () use ($targetObject, $name) {
            return $targetObject->$name;
        };
            $backtrace = debug_backtrace(true);
            $scopeObject = isset($backtrace[1]['object']) ? $backtrace[1]['object'] : new \stdClass();
            $accessor = $accessor->bindTo($scopeObject, get_class($scopeObject));
        $returnValue = & $accessor();

        return $returnValue;
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        $this->initializer593faf8e03e4c094214682 && $this->initializer593faf8e03e4c094214682->__invoke($this->valueHolder593faf8e03daf096421245, $this, '__set', array('name' => $name, 'value' => $value), $this->initializer593faf8e03e4c094214682);

        $realInstanceReflection = new \ReflectionClass(get_parent_class($this));

        if (! $realInstanceReflection->hasProperty($name)) {
            $targetObject = $this->valueHolder593faf8e03daf096421245;

            return $targetObject->$name = $value;;
            return;
        }

        $targetObject = $this->valueHolder593faf8e03daf096421245;
        $accessor = function & () use ($targetObject, $name, $value) {
            return $targetObject->$name = $value;
        };
            $backtrace = debug_backtrace(true);
            $scopeObject = isset($backtrace[1]['object']) ? $backtrace[1]['object'] : new \stdClass();
            $accessor = $accessor->bindTo($scopeObject, get_class($scopeObject));
        $returnValue = & $accessor();

        return $returnValue;
    }

    /**
     * @param string $name
     */
    public function __isset($name)
    {
        $this->initializer593faf8e03e4c094214682 && $this->initializer593faf8e03e4c094214682->__invoke($this->valueHolder593faf8e03daf096421245, $this, '__isset', array('name' => $name), $this->initializer593faf8e03e4c094214682);

        $realInstanceReflection = new \ReflectionClass(get_parent_class($this));

        if (! $realInstanceReflection->hasProperty($name)) {
            $targetObject = $this->valueHolder593faf8e03daf096421245;

            return isset($targetObject->$name);;
            return;
        }

        $targetObject = $this->valueHolder593faf8e03daf096421245;
        $accessor = function () use ($targetObject, $name) {
            return isset($targetObject->$name);
        };
            $backtrace = debug_backtrace(true);
            $scopeObject = isset($backtrace[1]['object']) ? $backtrace[1]['object'] : new \stdClass();
            $accessor = $accessor->bindTo($scopeObject, get_class($scopeObject));
        $returnValue = $accessor();

        return $returnValue;
    }

    /**
     * @param string $name
     */
    public function __unset($name)
    {
        $this->initializer593faf8e03e4c094214682 && $this->initializer593faf8e03e4c094214682->__invoke($this->valueHolder593faf8e03daf096421245, $this, '__unset', array('name' => $name), $this->initializer593faf8e03e4c094214682);

        $realInstanceReflection = new \ReflectionClass(get_parent_class($this));

        if (! $realInstanceReflection->hasProperty($name)) {
            $targetObject = $this->valueHolder593faf8e03daf096421245;

            unset($targetObject->$name);;
            return;
        }

        $targetObject = $this->valueHolder593faf8e03daf096421245;
        $accessor = function () use ($targetObject, $name) {
            unset($targetObject->$name);
        };
            $backtrace = debug_backtrace(true);
            $scopeObject = isset($backtrace[1]['object']) ? $backtrace[1]['object'] : new \stdClass();
            $accessor = $accessor->bindTo($scopeObject, get_class($scopeObject));
        $returnValue = $accessor();

        return $returnValue;
    }

    public function __clone()
    {
        $this->initializer593faf8e03e4c094214682 && $this->initializer593faf8e03e4c094214682->__invoke($this->valueHolder593faf8e03daf096421245, $this, '__clone', array(), $this->initializer593faf8e03e4c094214682);

        $this->valueHolder593faf8e03daf096421245 = clone $this->valueHolder593faf8e03daf096421245;
    }

    public function __sleep()
    {
        $this->initializer593faf8e03e4c094214682 && $this->initializer593faf8e03e4c094214682->__invoke($this->valueHolder593faf8e03daf096421245, $this, '__sleep', array(), $this->initializer593faf8e03e4c094214682);

        return array('valueHolder593faf8e03daf096421245');
    }

    public function __wakeup()
    {
    }

    /**
     * {@inheritDoc}
     */
    public function setProxyInitializer(\Closure $initializer = null)
    {
        $this->initializer593faf8e03e4c094214682 = $initializer;
    }

    /**
     * {@inheritDoc}
     */
    public function getProxyInitializer()
    {
        return $this->initializer593faf8e03e4c094214682;
    }

    /**
     * {@inheritDoc}
     */
    public function initializeProxy()
    {
        return $this->initializer593faf8e03e4c094214682 && $this->initializer593faf8e03e4c094214682->__invoke($this->valueHolder593faf8e03daf096421245, $this, 'initializeProxy', array(), $this->initializer593faf8e03e4c094214682);
    }

    /**
     * {@inheritDoc}
     */
    public function isProxyInitialized()
    {
        return null !== $this->valueHolder593faf8e03daf096421245;
    }

    /**
     * {@inheritDoc}
     */
    public function getWrappedValueHolderValue()
    {
        return $this->valueHolder593faf8e03daf096421245;
    }


}
