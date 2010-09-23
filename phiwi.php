<?php
/**
 * Phiwi: Prototyping and multiple inheritance for PHP 5.3+
 *
 * @package    Phiwi
 * @link       http://github.com/shadowhand/phiwi
 * @author     Woody Gilk <woody@wingsc.com>
 * @copyright  Copyright (c) 2010 Woody Gilk
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
class Phiwi {

	/**
	 * @var  array  prototype class storage
	 */
	public static $prototypes = array();

	/**
	 * Class prototype creation and storage.
	 *
	 *     Phiwi::proto('person', array(
	 *         'init' => function($self, $name) {
	 *             $self->name = $name;
	 *         },
	 *         'greet' => function($self, $other) {
	 *             return "Hello {$other->name}, I am {$self->name}.\n";
	 *         },
	 *     ));
	 *
	 * @param   string  class name
	 * @param   array   class methods
	 * @return  Phiwi
	 */
	public static function proto($name, array $methods = NULL)
	{
		if ( ! isset(self::$prototypes[$name]))
		{
			self::$prototypes[$name] = new Phiwi;
		}

		if ($methods)
		{
			self::$prototypes[$name]->_methods = array_merge(self::$prototypes[$name]->_methods, $methods);
		}

		return self::$prototypes[$name];
	}

	/**
	 * Extend a class prototype from another class.
	 *
	 *     Phiwi::extend('surfer', 'person', array(
	 *         'greet' => function($self, $other) {
	 *             return "Dude, {$other->name}! They call me {$self->name}.\n";
	 *         },
	 *     ));
	 *
	 * @param   string  class name
	 * @param   string  class to extend from
	 * @param   array   class methods to add or replace
	 * @return  Phiwi
	 * @uses    Phiwi::proto
	 */
	public static function extend($name, $from, array $methods = NULL)
	{
		if ( ! isset(self::$prototypes[$name]))
		{
			self::$prototypes[$name] = clone self::$prototypes[$from];
		}

		return self::proto($name, $methods);
	}

	/**
	 * Create a new instance of a class. Classes must define an "init" method
	 * before they can be created.
	 *
	 *     $bob = Phiwi::factory('person', 'Bob');
	 *     $joe = Phiwi::factory('surfer', 'Joe');
	 *
	 * @param   string  class name
	 * @param   mixed   additional init() argument
	 * @param   ...
	 * @return  Phiwi
	 */
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

	/**
	 * @var  array  class method (closure) storage
	 */
	public $_methods = array();

	/**
	 * Passes all class method calls into closure calls.
	 *
	 *     echo $bob->greet($joe);
	 *     echo $joe->greet($bob);
	 *
	 * @param   string  method name
	 * @param   array   method arguments
	 * @return  mixed
	 * @throws  BadMethodCallException
	 */
	public function __call($name, array $args)
	{
		if ( ! isset($this->_methods[$name]))
			throw new BadMethodCallException;

		// Closures cannot access $this
		array_unshift($args, $this);

		return call_user_func_array($this->_methods[$name], $args);
	}

	/**
	 * Mix another class into this class. Any duplicate class methods and
	 * properties will be replaced by the mixed class.
	 *
	 *     $joe->mixin($bob);
	 *
	 * Mixed in classes do not need to have the same prototype!
	 *
	 * @param   mixed  mixed in class, or prototyped class name
	 * @return  $this
	 * @uses    Phiwi::factory
	 */
	public function mixin($class)
	{
		if ( ! is_object($class))
		{
			$class = Phiwi::factory($class);
		}

		foreach ($class->_methods as $name => $method)
		{
			// Use references to ensure that changes to the imported
			// method will carry over the the base class.
			$this->_methods[$name] =& $class->_methods[$name];
		}

		$vars = get_object_vars($class);

		foreach ($vars as $var => $value)
		{
			if ($var[0] !== '_')
			{
				$this->$var = $value;
			}
		}

		return $this;
	}

} // End Phiwi
