<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use Nette\Object;
use Nette\Environment;
use Dibi;

/**
 * DI Container Factory.
 * @author Petr Procházka
 * @package Orm
 * @subpackage DI
 */
class ServiceContainerFactory extends Object implements IServiceContainerFactory
{
	/** @var IServiceContainer */
	private $container;

	/** @param IServiceContainer|NULL */
	public function __construct(IServiceContainer $container = NULL)
	{
		if (!$container) $container = new ServiceContainer;
		$container->addService('annotationClassParser', 'Orm\AnnotationClassParser');
		$container->addService('mapperFactory', array($this, 'createMapperFactory'));
		$container->addService('repositoryHelper', 'Orm\RepositoryHelper');
		$container->addService('dibi', array($this, 'createDibi'));
		if (class_exists('Nette\Environment'))
		{
			$container->addService('performanceHelperCache', array($this, 'createPerformanceHelperCache'));
		}
		$this->container = $container;
	}

	/** @return IServiceContainer */
	public function getContainer()
	{
		return $this->container;
	}

	/**
	 * @param IServiceContainer
	 * @return IMapperFactory
	 */
	public function createMapperFactory(IServiceContainer $container)
	{
		return new MapperFactory($container->getService('annotationClassParser', 'Orm\AnnotationClassParser'));
	}

	/** @return DibiConnection */
	public function createDibi()
	{
		return dibi::getConnection();
	}

	/** @return ArrayAccess */
	public function createPerformanceHelperCache()
	{
		return Environment::getCache('Orm\PerformanceHelper');
	}

}