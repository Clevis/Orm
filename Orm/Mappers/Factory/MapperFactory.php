<?php

namespace Orm;

use Nette\Object;

require_once __DIR__ . '/IMapperFactory.php';
require_once __DIR__ . '/AnnotationClassParser.php';

class MapperFactory extends Object implements IMapperFactory
{
	/** @var AnnotationClassParser */
	private $parser;

	/** @param AnnotationClassParser */
	public function __construct(AnnotationClassParser $parser)
	{
		$this->parser = $parser->register('mapper', 'Orm\IRepository', array($this, 'createDefaultMapperClass'));
	}

	/**
	 * @param IRepository
	 * @return IMapper
	 */
	public function createMapper(IRepository $repository)
	{
		$class = $this->parser->get('mapper', $repository);
		return new $class($repository);
	}

	/**
	 * FooRepository > FooMapper
	 * @param string
	 * @return string
	 */
	public function createDefaultMapperClass($repositoryClass)
	{
		$tmp = $repositoryClass;
		if (strtolower(substr($tmp, -10)) === 'repository')
		{
			$tmp = substr($tmp, 0, strlen($tmp) - 10);
		}
		return $tmp . 'Mapper';
	}

}
