<?php

/**
 * ApiGen 2.1.0 - API documentation generator for PHP 5.3+
 *
 * Copyright (c) 2010 David Grudl (http://davidgrudl.com)
 * Copyright (c) 2011 Jaroslav Hanslík (https://github.com/kukulich)
 * Copyright (c) 2011 Ondřej Nešpor (https://github.com/Andrewsville)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen;

use TokenReflection, TokenReflection\IReflectionClass, TokenReflection\IReflectionMethod, TokenReflection\IReflectionProperty, TokenReflection\IReflectionConstant;
use ReflectionMethod as InternalReflectionMethod, ReflectionProperty as InternalReflectionProperty;

/**
 * Class reflection envelope.
 *
 * Alters TokenReflection\IReflectionClass functionality for ApiGen.
 *
 * @author Jaroslav Hanslík
 * @author Ondřej Nešpor
 */
class ReflectionClass extends ReflectionBase
{
	/**
	 * Access level for methods.
	 *
	 * @var integer
	 */
	private $methodAccessLevels = false;

	/**
	 * Access level for properties.
	 *
	 * @var integer
	 */
	private $propertyAccessLevels = false;

	/**
	 * Cache for list of parent classes.
	 *
	 * @var array
	 */
	private $parentClasses;

	/**
	 * Cache for list of own methods.
	 *
	 * @var array
	 */
	private $ownMethods;

	/**
	 * Cache for list of own properties.
	 *
	 * @var array
	 */
	private $ownProperties;

	/**
	 * Cache for list of own constants.
	 *
	 * @var array
	 */
	private $ownConstants;

	/**
	 * Cache for list of all methods.
	 *
	 * @var array
	 */
	private $methods;

	/**
	 * Cache for list of all properties.
	 *
	 * @var array
	 */
	private $properties;

	/**
	 * Cache for list of all constants.
	 *
	 * @var array
	 */
	private $constants;

	/**
	 * Constructor.
	 *
	 * Sets the inspected class reflection.
	 *
	 * @param \TokenReflection\IReflectionClass $reflection Inspected class reflection
	 * @param \ApiGen\Generator $generator ApiGen generator
	 */
	public function __construct(IReflectionClass $reflection, Generator $generator)
	{
		parent::__construct($reflection, $generator);

		if (false === $this->methodAccessLevels) {
			if (count($this->config->accessLevels) < 3) {
				$this->methodAccessLevels = 0;
				$this->propertyAccessLevels = 0;

				foreach ($this->config->accessLevels as $level) {
					switch (strtolower($level)) {
						case 'public':
							$this->methodAccessLevels |= InternalReflectionMethod::IS_PUBLIC;
							$this->propertyAccessLevels |= InternalReflectionProperty::IS_PUBLIC;
							break;
						case 'protected':
							$this->methodAccessLevels |= InternalReflectionMethod::IS_PROTECTED;
							$this->propertyAccessLevels |= InternalReflectionProperty::IS_PROTECTED;
							break;
						case 'private':
							$this->methodAccessLevels |= InternalReflectionMethod::IS_PRIVATE;
							$this->propertyAccessLevels |= InternalReflectionProperty::IS_PRIVATE;
							break;
					}
				}
			} else {
				$this->methodAccessLevels = null;
				$this->propertyAccessLevels = null;
			}
		}
	}

	/**
	 * Returns visible methods.
	 *
	 * @return array
	 */
	public function getMethods()
	{
		if (null === $this->methods) {
			$this->methods = $this->getOwnMethods();
			foreach ($this->reflection->getMethods($this->methodAccessLevels) as $method) {
				if (isset($this->methods[$method->getName()])) {
					continue;
				}
				$apiMethod = new ReflectionMethod($method, $this->generator);
				if (!$this->isDocumented() || $apiMethod->isDocumented()) {
					$this->methods[$method->getName()] = $apiMethod;
				}
			}
		}
		return $this->methods;
	}

	/**
	 * Returns visible methods declared by inspected class.
	 *
	 * @return array
	 */
	public function getOwnMethods()
	{
		if (null === $this->ownMethods) {
			$this->ownMethods = array();
			foreach ($this->reflection->getOwnMethods($this->methodAccessLevels) as $method) {
				$apiMethod = new ReflectionMethod($method, $this->generator);
				if (!$this->isDocumented() || $apiMethod->isDocumented()) {
					$this->ownMethods[$method->getName()] = $apiMethod;
				}
			}
		}
		return $this->ownMethods;
	}

	/**
	 * Returns visible methods declared by traits.
	 *
	 * @return array
	 */
	public function getTraitMethods()
	{
		$methods = array();
		foreach ($this->reflection->getTraitMethods($this->methodAccessLevels) as $method) {
			$apiMethod = new ReflectionMethod($method, $this->generator);
			if (!$this->isDocumented() || $apiMethod->isDocumented()) {
				$methods[$method->getName()] = $apiMethod;
			}
		}
		return $methods;
	}

	/**
	 * Returns a method reflection.
	 *
	 * @param string $name Method name
	 * @return \ApiGen\ReflectionMethod
	 */
	public function getMethod($name)
	{
		if ($this->hasMethod($name)) {
			return $this->methods[$name];
		}

		throw new \InvalidArgumentException(sprintf('Method %s does not exist in class %s', $name, $this->reflection->getName()));
	}

	/**
	 * Returns visible properties.
	 *
	 * @return array
	 */
	public function getProperties()
	{
		if (null === $this->properties) {
			$this->properties = $this->getOwnProperties();
			foreach ($this->reflection->getProperties($this->propertyAccessLevels) as $property) {
				if (isset($this->properties[$property->getName()])) {
					continue;
				}
				$apiProperty = new ReflectionProperty($property, $this->generator);
				if (!$this->isDocumented() || $apiProperty->isDocumented()) {
					$this->properties[$property->getName()] = $apiProperty;
				}
			}
		}
		return $this->properties;
	}


	/**
	 * Returns visible properties declared by inspected class.
	 *
	 * @return array
	 */
	public function getOwnProperties()
	{
		if (null === $this->ownProperties) {
			$this->ownProperties = array();
			foreach ($this->reflection->getOwnProperties($this->propertyAccessLevels) as $property) {
				$apiProperty = new ReflectionProperty($property, $this->generator);
				if (!$this->isDocumented() || $apiProperty->isDocumented()) {
					$this->ownProperties[$property->getName()] = $apiProperty;
				}
			}
		}
		return $this->ownProperties;
	}

	/**
	 * Returns visible properties declared by traits.
	 *
	 * @return array
	 */
	public function getTraitProperties()
	{
		$properties = array();
		foreach ($this->reflection->getTraitProperties($this->propertyAccessLevels) as $property) {
			$apiProperty = new ReflectionProperty($property, $this->generator);
			if (!$this->isDocumented() || $apiProperty->isDocumented()) {
				$properties[$property->getName()] = $apiProperty;
			}
		}
		return $properties;
	}

	/**
	 * Returns a method property.
	 *
	 * @param string $name Method name
	 * @return \ApiGen\ReflectionProperty
	 */
	public function getProperty($name)
	{
		if ($this->hasProperty($name)) {
			return $this->properties[$name];
		}

		throw new \InvalidArgumentException(sprintf('Property %s does not exist in class %s', $name, $this->reflection->getName()));
	}

	/**
	 * Returns visible properties.
	 *
	 * @return array
	 */
	public function getConstants()
	{
		if (null === $this->constants) {
			$this->constants = array();
			foreach ($this->reflection->getConstantReflections() as $constant) {
				$apiConstant = new ReflectionConstant($constant, $this->generator);
				if (!$this->isDocumented() || $apiConstant->isDocumented()) {
					$this->constants[$constant->getName()] = $apiConstant;
				}
			}
		}

		return $this->constants;
	}

	/**
	 * Returns constants declared by inspected class.
	 *
	 * @return array
	 */
	public function getOwnConstants()
	{
		if (null === $this->ownConstants) {
			$this->ownConstants = array();
			$className = $this->reflection->getName();
			foreach ($this->getConstants() as $constantName => $constant) {
				if ($className === $constant->getDeclaringClassName()) {
					$this->ownConstants[$constantName] = $constant;
				}
			}
		}
		return $this->ownConstants;
	}

	/**
	 * Returns a constant reflection.
	 *
	 * @param string $name Constant name
	 * @return \ApiGen\ReflectionConstant
	 */
	public function getConstantReflection($name)
	{
		if (null === $this->constants) {
			$this->getConstants();
		}

		if (isset($this->constants[$name])) {
			return $this->constants[$name];
		}

		throw new \InvalidArgumentException(sprintf('Constant %s does not exist in class %s', $name, $this->reflection->getName()));
	}

	/**
	 * Returns a constant reflection.
	 *
	 * @param string $name Constant name
	 * @return \ApiGen\ReflectionConstant
	 */
	public function getConstant($name)
	{
		return $this->getConstantReflection($name);
	}

	/**
	 * Checks if there is a constant of the given name.
	 *
	 * @param string $constantName Constant name
	 * @return boolean
	 */
	public function hasConstant($constantName)
	{
		if (null === $this->constants) {
			$this->getConstants();
		}

		return isset($this->constants[$constantName]);
	}

	/**
	 * Checks if there is a constant of the given name.
	 *
	 * @param string $constantName Constant name
	 * @return boolean
	 */
	public function hasOwnConstant($constantName)
	{
		if (null === $this->ownConstants) {
			$this->getOwnConstants();
		}

		return isset($this->ownConstants[$constantName]);
	}

	/**
	 * Returns a constant reflection.
	 *
	 * @param string $name Constant name
	 * @return \ApiGen\ReflectionConstant
	 */
	public function getOwnConstantReflection($name)
	{
		if (null === $this->ownConstants) {
			$this->getOwnConstants();
		}

		if (isset($this->ownConstants[$name])) {
			return $this->ownConstants[$name];
		}

		throw new \InvalidArgumentException(sprintf('Constant %s does not exist in class %s', $name, $this->reflection->getName()));
	}

	/**
	 * Returns a constant reflection.
	 *
	 * @param string $name Constant name
	 * @return \ApiGen\ReflectionConstant
	 */
	public function getOwnConstant($name)
	{
		return $this->getOwnConstantReflection($name);
	}

	/**
	 * Returns a parent class reflection encapsulated by this class.
	 *
	 * @return \ApiGen\ReflectionClass
	 */
	public function getParentClass()
	{
		if ($className = $this->reflection->getParentClassName()) {
			return $this->classes[$className];
		}
		return $className;
	}

	/**
	 * Returns all parent classes reflections encapsulated by this class.
	 *
	 * @return array
	 */
	public function getParentClasses()
	{
		if (null === $this->parentClasses) {
			$classes = $this->classes;
			$this->parentClasses = array_map(function(IReflectionClass $class) use ($classes) {
				return $classes[$class->getName()];
			}, $this->reflection->getParentClasses());
		}
		return $this->parentClasses;
	}

	/**
	 * Returns all interface reflections encapsulated by this class.
	 *
	 * @return array
	 */
	public function getInterfaces()
	{
		$classes = $this->classes;
		return array_map(function(IReflectionClass $class) use ($classes) {
			return $classes[$class->getName()];
		}, $this->reflection->getInterfaces());
	}

	/**
	 * Returns all interfaces implemented by the inspected class and not its parents.
	 *
	 * @return array
	 */
	public function getOwnInterfaces()
	{
		$classes = $this->classes;
		return array_map(function(IReflectionClass $class) use ($classes) {
			return $classes[$class->getName()];
		}, $this->reflection->getOwnInterfaces());
	}

	/**
	 * Returns all traits reflections encapsulated by this class.
	 *
	 * @return array
	 */
	public function getTraits()
	{
		$classes = $this->classes;
		return array_map(function(IReflectionClass $class) use ($classes) {
			return $classes[$class->getName()];
		}, $this->reflection->getTraits());
	}

	/**
	 * Returns all traits used by the inspected class and not its parents.
	 *
	 * @return array
	 */
	public function getOwnTraits()
	{
		$classes = $this->classes;
		return array_map(function(IReflectionClass $class) use ($classes) {
			return $classes[$class->getName()];
		}, $this->reflection->getOwnTraits());
	}

	/**
	 * Returns reflections of direct subclasses.
	 *
	 * @return array
	 */
	public function getDirectSubClasses()
	{
		$subClasses = array();
		$name = $this->reflection->getName();
		foreach ($this->classes as $class) {
			if (!$class->isDocumented()) {
				continue;
			}
			if ($name === $class->getParentClassName()) {
				$subClasses[] = $class;
			}
		}
		return $subClasses;
	}

	/**
	 * Returns reflections of indirect subclasses.
	 *
	 * @return array
	 */
	public function getIndirectSubClasses()
	{
		$subClasses = array();
		$name = $this->reflection->getName();
		foreach ($this->classes as $class) {
			if (!$class->isDocumented()) {
				continue;
			}
			if ($name !== $class->getParentClassName() && $class->isSubclassOf($name)) {
				$subClasses[] = $class;
			}
		}
		return $subClasses;
	}

	/**
	 * Returns reflections of classes directly implementing this interface.
	 *
	 * @return array
	 */
	public function getDirectImplementers()
	{
		if (!$this->isInterface()) {
			return array();
		}

		$implementers = array();
		$name = $this->reflection->getName();
		foreach ($this->classes as $class) {
			if (!$class->isDocumented()) {
				continue;
			}
			if (in_array($name, $class->getOwnInterfaceNames())) {
				$implementers[] = $class;
			}
		}
		return $implementers;
	}

	/**
	 * Returns reflections of classes indirectly implementing this interface.
	 *
	 * @return array
	 */
	public function getIndirectImplementers()
	{
		if (!$this->isInterface()) {
			return array();
		}

		$implementers = array();
		$name = $this->reflection->getName();
		foreach ($this->classes as $class) {
			if (!$class->isDocumented()) {
				continue;
			}
			if ($class->implementsInterface($name) && !in_array($name, $class->getOwnInterfaceNames())) {
				$implementers[] = $class;
			}
		}
		return $implementers;
	}

	/**
	 * Returns reflections of classes directly using this trait.
	 *
	 * @return array
	 */
	public function getDirectUsers()
	{
		if (!$this->isTrait()) {
			return array();
		}

		$users = array();
		$name = $this->reflection->getName();
		foreach ($this->classes as $class) {
			if (!$class->isDocumented()) {
				continue;
			}

			if (in_array($name, $class->getOwnTraitNames())) {
				$users[] = $class;
			}
		}
		return $users;
	}

	/**
	 * Returns reflections of classes indirectly using this trait.
	 *
	 * @return array
	 */
	public function getIndirectUsers()
	{
		if (!$this->isTrait()) {
			return array();
		}

		$users = array();
		$name = $this->reflection->getName();
		foreach ($this->classes as $class) {
			if (!$class->isDocumented()) {
				continue;
			}
			if ($class->usesTrait($name) && !in_array($name, $class->getOwnTraitNames())) {
				$users[] = $class;
			}
		}
		return $users;
	}

	/**
	 * Returns an array of inherited methods from parent classes grouped by the declaring class name.
	 *
	 * @return array
	 */
	public function getInheritedMethods()
	{
		$methods = array();
		$allMethods = array_flip(array_map(function($method) {
			return $method->getName();
		}, $this->getOwnMethods()));

		foreach (array_merge($this->getParentClasses(), $this->getInterfaces()) as $class) {
			$inheritedMethods = array();
			foreach ($class->getOwnMethods() as $method) {
				if (!array_key_exists($method->getName(), $allMethods) && !$method->isPrivate()) {
					$inheritedMethods[$method->getName()] = $method;
					$allMethods[$method->getName()] = null;
				}
			}

			if (!empty($inheritedMethods)) {
				ksort($inheritedMethods);
				$methods[$class->getName()] = array_values($inheritedMethods);
			}
		}

		return $methods;
	}

	/**
	 * Returns an array of used methods from used traits grouped by the declaring trait name.
	 *
	 * @return array
	 */
	public function getUsedMethods()
	{
		$usedMethods = array();
		foreach ($this->getMethods() as $method) {
			if (null === $method->getDeclaringTraitName()) {
				continue;
			}

			if (null === $method->getOriginalName() || $method->getName() === $method->getOriginalName()) {
				$usedMethods[$method->getDeclaringTraitName()][$method->getName()]['method'] = $method;
			} else {
				$usedMethods[$method->getDeclaringTraitName()][$method->getOriginalName()]['aliases'][$method->getName()] = $method;
			}
		}

		// Sort
		array_walk($usedMethods, function(&$methods) {
			ksort($methods);
			array_walk($methods, function(&$aliasedMethods) {
				if (!isset($aliasedMethods['aliases'])) {
					$aliasedMethods['aliases'] = array();
				}
				ksort($aliasedMethods['aliases']);
			});
		});

		return $usedMethods;
	}

	/**
	 * Returns an array of inherited constants from parent classes grouped by the declaring class name.
	 *
	 * @return array
	 */
	public function getInheritedConstants()
	{
		return array_filter(
			array_map(
				function(ReflectionClass $class) {
					$reflections = $class->getOwnConstants();
					ksort($reflections);
					return $reflections;
				},
				array_merge($this->getParentClasses(), $this->getInterfaces())
			)
		);
	}

	/**
	 * Returns an array of inherited properties from parent classes grouped by the declaring class name.
	 *
	 * @return array
	 */
	public function getInheritedProperties()
	{
		$properties = array();
		$allProperties = array_flip(array_map(function($property) {
			return $property->getName();
		}, $this->getOwnProperties()));

		foreach ($this->getParentClasses() as $class) {
			$inheritedProperties = array();
			foreach ($class->getOwnProperties() as $property) {
				if (!array_key_exists($property->getName(), $allProperties) && !$property->isPrivate()) {
					$inheritedProperties[$property->getName()] = $property;
					$allProperties[$property->getName()] = null;
				}
			}

			if (!empty($inheritedProperties)) {
				ksort($inheritedProperties);
				$properties[$class->getName()] = array_values($inheritedProperties);
			}
		}

		return $properties;
	}

	/**
	 * Returns an array of used properties from used traits grouped by the declaring trait name.
	 *
	 * @return array
	 */
	public function getUsedProperties()
	{
		$properties = array();
		$allProperties = array_flip(array_map(function($property) {
			return $property->getName();
		}, $this->getOwnProperties()));

		foreach ($this->getTraits() as $trait) {
			$usedProperties = array();
			foreach ($trait->getOwnProperties() as $property) {
				if (!array_key_exists($property->getName(), $allProperties)) {
					$usedProperties[$property->getName()] = $property;
					$allProperties[$property->getName()] = null;
				}
			}

			if (!empty($usedProperties)) {
				ksort($usedProperties);
				$properties[$trait->getName()] = array_values($usedProperties);
			}
		}

		return $properties;
	}

	/**
	 * Checks if there is a property of the given name.
	 *
	 * @param string $propertyName Property name
	 * @return boolean
	 */
	public function hasProperty($propertyName)
	{
		if (null === $this->properties) {
			$this->getProperties();
		}

		return isset($this->properties[$propertyName]);
	}

	/**
	 * Checks if there is a property of the given name.
	 *
	 * @param string $propertyName Property name
	 * @return boolean
	 */
	public function hasOwnProperty($propertyName)
	{
		if (null === $this->ownProperties) {
			$this->getOwnProperties();
		}

		return isset($this->ownProperties[$propertyName]);
	}

	/**
	 * Checks if there is a property of the given name.
	 *
	 * @param string $propertyName Property name
	 * @return boolean
	 */
	public function hasTraitProperty($propertyName)
	{
		$properties = $this->getTraitProperties();
		return isset($properties[$propertyName]);
	}

	/**
	 * Checks if there is a method of the given name.
	 *
	 * @param string $methodName Method name
	 * @return boolean
	 */
	public function hasMethod($methodName)
	{
		if (null === $this->methods) {
			$this->getMethods();
		}

		return isset($this->methods[$methodName]);
	}

	/**
	 * Checks if there is a method of the given name.
	 *
	 * @param string $methodName Method name
	 * @return boolean
	 */
	public function hasOwnMethod($methodName)
	{
		if (null === $this->ownMethods) {
			$this->getOwnMethods();
		}

		return isset($this->ownMethods[$methodName]);
	}

	/**
	 * Checks if there is a method of the given name.
	 *
	 * @param string $methodName Method name
	 * @return boolean
	 */
	public function hasTraitMethod($methodName)
	{
		$methods = $this->getTraitMethods();
		return isset($methods[$methodName]);
	}

	/**
	 * Returns if the class should be documented.
	 *
	 * @return boolean
	 */
	public function isDocumented()
	{
		if (null === $this->isDocumented && parent::isDocumented()) {
			foreach ($this->config->skipDocPath as $mask) {
				if (fnmatch($mask, $this->reflection->getFilename(), FNM_NOESCAPE)) {
					$this->isDocumented = false;
					break;
				}
			}
			if (true === $this->isDocumented) {
				foreach ($this->config->skipDocPrefix as $prefix) {
					if (0 === strpos($this->reflection->getName(), $prefix)) {
						$this->isDocumented = false;
						break;
					}
				}
			}
		}

		return $this->isDocumented;
	}
}
