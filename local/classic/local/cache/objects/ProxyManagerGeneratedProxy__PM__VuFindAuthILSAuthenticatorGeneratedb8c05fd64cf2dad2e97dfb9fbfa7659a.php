<?php

namespace ProxyManagerGeneratedProxy\__PM__\VuFind\Auth\ILSAuthenticator;

class Generatedb8c05fd64cf2dad2e97dfb9fbfa7659a extends \VuFind\Auth\ILSAuthenticator implements \ProxyManager\Proxy\VirtualProxyInterface
{

    /**
     * @var \Closure|null initializer responsible for generating the wrapped object
     */
    private $valueHolder59400162472fa638942682 = null;

    /**
     * @var \Closure|null initializer responsible for generating the wrapped object
     */
    private $initializer5940016247382028104520 = null;

    /**
     * @var bool[] map of public properties of the parent class
     */
    private static $publicProperties5940016247158113993212 = array(
        
    );

    private static $signatureb8c05fd64cf2dad2e97dfb9fbfa7659a = 'YTozOntzOjk6ImNsYXNzTmFtZSI7czoyODoiVnVGaW5kXEF1dGhcSUxTQXV0aGVudGljYXRvciI7czo3OiJmYWN0b3J5IjtzOjUwOiJQcm94eU1hbmFnZXJcRmFjdG9yeVxMYXp5TG9hZGluZ1ZhbHVlSG9sZGVyRmFjdG9yeSI7czoxOToicHJveHlNYW5hZ2VyVmVyc2lvbiI7czo1OiIxLjAuMCI7fQ==';

    /**
     * {@inheritDoc}
     */
    public function getStoredCatalogCredentials()
    {
        $this->initializer5940016247382028104520 && $this->initializer5940016247382028104520->__invoke($this->valueHolder59400162472fa638942682, $this, 'getStoredCatalogCredentials', array(), $this->initializer5940016247382028104520);

        return $this->valueHolder59400162472fa638942682->getStoredCatalogCredentials();
    }

    /**
     * {@inheritDoc}
     */
    public function storedCatalogLogin()
    {
        $this->initializer5940016247382028104520 && $this->initializer5940016247382028104520->__invoke($this->valueHolder59400162472fa638942682, $this, 'storedCatalogLogin', array(), $this->initializer5940016247382028104520);

        return $this->valueHolder59400162472fa638942682->storedCatalogLogin();
    }

    /**
     * {@inheritDoc}
     */
    public function newCatalogLogin($username, $password)
    {
        $this->initializer5940016247382028104520 && $this->initializer5940016247382028104520->__invoke($this->valueHolder59400162472fa638942682, $this, 'newCatalogLogin', array('username' => $username, 'password' => $password), $this->initializer5940016247382028104520);

        return $this->valueHolder59400162472fa638942682->newCatalogLogin($username, $password);
    }

    /**
     * @override constructor for lazy initialization
     *
     * @param \Closure|null $initializer
     */
    public function __construct($initializer)
    {
        $this->initializer5940016247382028104520 = $initializer;
    }

    /**
     * @param string $name
     */
    public function & __get($name)
    {
        $this->initializer5940016247382028104520 && $this->initializer5940016247382028104520->__invoke($this->valueHolder59400162472fa638942682, $this, '__get', array('name' => $name), $this->initializer5940016247382028104520);

        if (isset(self::$publicProperties5940016247158113993212[$name])) {
            return $this->valueHolder59400162472fa638942682->$name;
        }

        $realInstanceReflection = new \ReflectionClass(get_parent_class($this));

        if (! $realInstanceReflection->hasProperty($name)) {
            $targetObject = $this->valueHolder59400162472fa638942682;

            $backtrace = debug_backtrace(false);
            trigger_error('Undefined property: ' . get_parent_class($this) . '::$' . $name . ' in ' . $backtrace[0]['file'] . ' on line ' . $backtrace[0]['line'], \E_USER_NOTICE);
            return $targetObject->$name;;
            return;
        }

        $targetObject = $this->valueHolder59400162472fa638942682;
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
        $this->initializer5940016247382028104520 && $this->initializer5940016247382028104520->__invoke($this->valueHolder59400162472fa638942682, $this, '__set', array('name' => $name, 'value' => $value), $this->initializer5940016247382028104520);

        $realInstanceReflection = new \ReflectionClass(get_parent_class($this));

        if (! $realInstanceReflection->hasProperty($name)) {
            $targetObject = $this->valueHolder59400162472fa638942682;

            return $targetObject->$name = $value;;
            return;
        }

        $targetObject = $this->valueHolder59400162472fa638942682;
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
        $this->initializer5940016247382028104520 && $this->initializer5940016247382028104520->__invoke($this->valueHolder59400162472fa638942682, $this, '__isset', array('name' => $name), $this->initializer5940016247382028104520);

        $realInstanceReflection = new \ReflectionClass(get_parent_class($this));

        if (! $realInstanceReflection->hasProperty($name)) {
            $targetObject = $this->valueHolder59400162472fa638942682;

            return isset($targetObject->$name);;
            return;
        }

        $targetObject = $this->valueHolder59400162472fa638942682;
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
        $this->initializer5940016247382028104520 && $this->initializer5940016247382028104520->__invoke($this->valueHolder59400162472fa638942682, $this, '__unset', array('name' => $name), $this->initializer5940016247382028104520);

        $realInstanceReflection = new \ReflectionClass(get_parent_class($this));

        if (! $realInstanceReflection->hasProperty($name)) {
            $targetObject = $this->valueHolder59400162472fa638942682;

            unset($targetObject->$name);;
            return;
        }

        $targetObject = $this->valueHolder59400162472fa638942682;
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
        $this->initializer5940016247382028104520 && $this->initializer5940016247382028104520->__invoke($this->valueHolder59400162472fa638942682, $this, '__clone', array(), $this->initializer5940016247382028104520);

        $this->valueHolder59400162472fa638942682 = clone $this->valueHolder59400162472fa638942682;
    }

    public function __sleep()
    {
        $this->initializer5940016247382028104520 && $this->initializer5940016247382028104520->__invoke($this->valueHolder59400162472fa638942682, $this, '__sleep', array(), $this->initializer5940016247382028104520);

        return array('valueHolder59400162472fa638942682');
    }

    public function __wakeup()
    {
    }

    /**
     * {@inheritDoc}
     */
    public function setProxyInitializer(\Closure $initializer = null)
    {
        $this->initializer5940016247382028104520 = $initializer;
    }

    /**
     * {@inheritDoc}
     */
    public function getProxyInitializer()
    {
        return $this->initializer5940016247382028104520;
    }

    /**
     * {@inheritDoc}
     */
    public function initializeProxy()
    {
        return $this->initializer5940016247382028104520 && $this->initializer5940016247382028104520->__invoke($this->valueHolder59400162472fa638942682, $this, 'initializeProxy', array(), $this->initializer5940016247382028104520);
    }

    /**
     * {@inheritDoc}
     */
    public function isProxyInitialized()
    {
        return null !== $this->valueHolder59400162472fa638942682;
    }

    /**
     * {@inheritDoc}
     */
    public function getWrappedValueHolderValue()
    {
        return $this->valueHolder59400162472fa638942682;
    }


}
