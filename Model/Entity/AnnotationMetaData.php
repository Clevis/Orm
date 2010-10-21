<?php

require_once dirname(__FILE__) . '/MetaData.php';

class AnnotationMetaData extends Object
{
	public static function getEntityParams($class)
	{
		$metaData = new MetaData($class);
		$class = $metaData->getEntityClass();
		$classes = array();
		while (class_exists($class))
		{
			if ($class === 'Object') break;
			$classes[] = $class;
			if ($class === 'Entity') break; // todo
			$class = get_parent_class($class);
		}

		foreach (array_reverse($classes) as $class)
		{
			$annotations = AnnotationsParser::getAll(new ClassReflection($class));

			if (isset($annotations['property']))
			{
				foreach ($annotations['property'] as $string)
				{
					if (preg_match('#^(-read|-write)?\s?([a-z0-9_\|]+)\s+\$([a-z0-9_]+)($|\s(.*)$)#si', $string, $match))
					{
						$property = $match[3];
						$type = $match[2];
						$mode = $match[1];
						$string = $match[4];
					}
					else if (preg_match('#^(-read|-write)?\s?\$([a-z0-9_]+)\s+([a-z0-9_\|]+)($|\s(.*)$)#si', $string, $match))
					{
						$property = $match[2];
						$type = $match[3];
						$mode = $match[1];
						$string = $match[4];
					}
					else if (preg_match('#^(-read|-write)?\s?\$([a-z0-9_]+)($|\s(.*)$)#si', $string, $match))
					{
						$property = $match[2];
						$type = 'mixed';
						$mode = $match[1];
						$string = $match[3];
					}
					else
					{
						throw new InvalidStateException($string);
					}

					$mode = ((!$mode OR $mode === '-read') ? MetaData::READ : 0) | ((!$mode OR $mode === '-write') ? MetaData::WRITE : 0);

					$property = $metaData->addProperty($property, $type, $mode, $class);
					self::$property = $property;
					$string = preg_replace_callback('#\{\s*([^\s\}]+)(?:\s+([^\}]*))?\s*\}#si', array(__CLASS__, 'addProperty'), $string);
					self::$property = NULL;

					if (preg_match('#\{|\}#',$string)) throw new Exception($string);

				}
			}

			if (isset($annotations['fk']) OR isset($annotations['foreignKey']))
			{
				throw new DeprecatedException("Annotation @fk and @foreignKey is deprecated use {1:1 repo} instead; in {$class}.");
			}
		}
		return $metaData;
	}

	static private $property;

	static private $aliases = array(
		'1:1' => 'onetoone',
		'm:1' => 'manytoone',
		'n:1' => 'manytoone',
		'm:m' => 'manytomany',
		'n:n' => 'manytomany',
		'm:n' => 'manytomany',
		'n:m' => 'manytomany',
		'1:m' => 'onetomany',
		'1:n' => 'onetomany',
	);

	private static function addProperty($match)
	{
		$property = self::$property;

		$name = strtolower($match[1]);
		if (isset(self::$aliases[$name])) $name = self::$aliases[$name];
		$method = "set{$name}";
		if (!method_exists($property, $method)) throw new Exception($name);
		$params = isset($match[2]) ? $match[2] : NULL;
		$paramMethod = "builtParams{$name}";
		if (method_exists($property, $paramMethod))
		{
			$params = $property->$paramMethod($params);
		}
		else
		{
			$params = array($params);
		}
		call_user_func_array(array($property, $method), $params);
	}

}