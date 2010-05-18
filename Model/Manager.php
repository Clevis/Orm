<?php

class EntityManager extends Object
{
	static $cache = array();
	public static function getEntityParams($class) // todo castecne presunout do entity, aby se dalo prepsat chovani dinamicky
	{
		if (!class_exists($class)) throw new InvalidStateException();
		else if (!is_subclass_of($class, 'Entity')) throw new InvalidStateException();
		
		if (!isset(self::$cache[$class]))
		{
			$params = array();
			$classes = array();
			$_class = $class;
			while (class_exists($_class))
			{
				$classes[] = $_class;
				if ($_class === 'Entity') break;
				$_class = get_parent_class($_class);
			}
			
			foreach ($classes as $_class)
			{
				$annotations = AnnotationsParser::getAll(new ClassReflection($_class));
				
				if (isset($annotations['property']))
				{
					foreach ($annotations['property'] as $property)
					{
						
						if (preg_match('#^(-read|-write)?\s?([a-z0-9_\|]+)\s+\$([a-z0-9_]+)($|\s)#si', $property, $match))
						{
							$property = $match[3];
							$type = $match[2];
							$mode = $match[1];
						}
						else if (preg_match('#^(-read|-write)?\s?\$([a-z0-9_]+)\s+([a-z0-9_\|]+)($|\s)#si', $property, $match))
						{
							$property = $match[2];
							$type = $match[3];
							$mode = $match[1];
						}
						else if (preg_match('#^(-read|-write)?\s?\$([a-z0-9_]+)($|\s)#si', $property, $match))
						{
							$property = $match[2];
							$type = 'mixed';
							$mode = $match[1];
						}
						else
						{
							throw new InvalidStateException($property);
							//continue;
						}
						
						$type = explode('|',strtolower($type));
						if (in_array('mixed', $type))
						{
							$type = array();
						}
						
						if (isset($params[$property]['types']) AND $params[$property]['types'] !== $type)
						{
							throw new InvalidStateException('Getter and setter types must be same.');	
						}
						
						$params[$property]['types'] = $type;
						
						if (!$mode OR $mode === '-read')
						{
							$params[$property]['get'] = array('method' => NULL);
						}
						if (!$mode OR $mode === '-write')
						{
							$params[$property]['set'] = array('method' => NULL);
						}
						
					}
				}
				
				if (isset($annotations['fk']))
				{
					if (isset($annotations['foreignKey']))
					{
						$annotations['foreignKey'] = array_merge($annotations['foreignKey'], $annotations['fk']);
					}
					else
					{
						$annotations['foreignKey'] = $annotations['fk'];
					}
				}
				if (isset($annotations['foreignKey']))
				{
					foreach ($annotations['foreignKey'] as $fk)
					{
						if (preg_match('#\s?\$([a-z0-9_]+)\s([a-z0-9_]+)$#si', $fk, $match))
						{
							$property = $match[1];
							$repository = $match[2];
							if (isset($params[$property]))
							{
								if (Model::getRepository($repository) instanceof Repository)
								{
									$params[$property]['fk'] = $repository;
								}
								else throw new InvalidStateException($repository);
							}
							else throw new InvalidStateException($property);
						}
						else throw new InvalidStateException();
					}
				}
				
				/*if (isset($annotations['method']))
				{
					foreach ($annotations['method'] as $method)
					{
						
					}
				}*/
			}
			
			$methods = array_diff(get_class_methods($class), get_class_methods('Entity'));
			foreach ($methods as $method)
			{
				$m = substr($method, 0, 3);
				if ($m === 'get' OR $m === 'set')
				{
					$var = substr($method, 3);
					$var{0} = strtolower($var{0});
					if (isset($params[$var][$m]))
					{
						$params[$var][$m]['method'] = $method;
					}
				}
			}
			
			self::$cache[$class] = $params;
		}
		
		return self::$cache[$class];
	}
	
	public static function isParamValid(array $types, & $value)
	{
		$_value = $value;
		
		if ($types === array()) return true; // mean mixed
		
		foreach ($types as $type)
		{
			if ($type === 'void' OR $type === 'null')
			{
				if ($value === NULL) return true;
				continue;
			}
			else if (!in_array($type, array('string', 'float', 'int', 'bool', 'array', 'object')))
			{
				if ($value instanceof $type) return true;
				continue;
			}
			else if ($type === 'mixed') return true;
			else
			{
				if (call_user_func("is_$type", $value)) return true;
				else
				{
					if (in_array($type, array('float', 'int')) AND is_numeric($value) OR empty($value))
					{
						$_value = $value;
						settype($_value, $type);
					}
					else if (in_array($type, array('array', 'object')) AND (is_array($value) OR is_object($value)))
					{
						$_value = $value;
						settype($_value, $type);
					}
					else if ($type === 'string' AND (is_int($value) OR is_float($value) OR (is_object($value) AND method_exists($value, '__toString'))))
					{
						$_value = (string) $value;
					}
					else if ($type === 'bool')
					{
						$_value = (bool) $value;
					}
					continue;
				}
			}
		
		}
		
		if ($_value === $value)
		{
			return false;
		}
		else
		{
			$value = $_value;
			return true;
		}
		
	}
	
}
class Manager extends EntityManager {}