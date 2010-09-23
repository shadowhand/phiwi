<?php

class Phiwi {

	public static $prototypes = array();

	public static function proto($name, array $methods = NULL)
	{
		if ( ! isset(self::$prototypes[$name]))
		{
			self::$prototypes[$name] = new Phiwi;
		}

		if ($methods)
		{
			self::$prototypes[$name]->methods = array_merge(self::$prototypes[$name]->methods, $methods);
		}

		return self::$prototypes[$name];
	}

	public static function extend($name, $from, array $methods = NULL)
	{
		if ( ! isset(self::$prototypes[$name]))
		{
			self::$prototypes[$name] = clone self::$prototypes[$from];
		}

		return self::proto($name, $methods);
	}

	public static function factory($name)
	{
		if ( ! isset(self::$prototypes[$name]))
			throw new InvalidArgumentException("Prototype of {$name} not yet defined");

		// Clone the prototype
		$class = clone self::$prototypes[$name];

		// Get all arguments, will be passed to init()
		$args = func_get_args();

		// Remove $name
		array_shift($args);

		call_user_func_array(array($class, 'init'), $args);

		return $class;
	}

	public $methods = array();

	public function __call($name, array $args)
	{
		if ( ! isset($this->methods[$name]))
			throw new BadMethodCallException;

		// Closures cannot access $this
		array_unshift($args, $this);

		return call_user_func_array($this->methods[$name], $args);
	}

} // End Phiwi
