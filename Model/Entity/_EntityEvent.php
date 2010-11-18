<?php

/**
 * Udalosti
 * @see Entity
 */
abstract class _EntityEvent extends _EntityMeta
{
	/**
	 * Pro kontrolu jestli bylo v podedene udalosti volano parent::on*.
	 * Obrahuje nazev posledni volane udalosti.
	 * @var string|NULL
	 */
	private $checkEvent;

	public function __construct()
	{
		$this->___event($this, 'create');
	}

	/** Vytvorena nova entita */
	protected function onCreate()
	{
		$this->checkEvent = 'onCreate';
	}

	/**
	 * Vytazena z mapperu
	 * @param IRepository
	 * @param array
	 */
	protected function onLoad(IRepository $repository, array $data)
	{
		$this->checkEvent = 'onLoad';
	}

	/**
	 * Pred persistovanim (insert nebo update)
	 * @param IRepository
	 */
	protected function onBeforePersist(IRepository $repository)
	{
		$this->checkEvent = 'onBeforePersist';
	}

	/**
	 * Po persistovani (insert nebo update)
	 * @param IRepository
	 */
	protected function onAfterPersist(IRepository $repository)
	{
		$this->checkEvent = 'onAfterPersist';
	}

	/**
	 * Behem persistovani, vsechny subentity nemusi byt jeste persistovany
	 * @param IRepository
	 * @param int
	 */
	protected function onPersist(IRepository $repository, $id)
	{
		$this->checkEvent = 'onPersist';
	}

	/**
	 * Pred vymazanim
	 * @param IRepository
	 */
	protected function onBeforeDelete(IRepository $repository)
	{
		$this->checkEvent = 'onBeforeDelete';
	}

	/**
	 * Po vymazani
	 * @param IRepository
	 */
	protected function onAfterDelete(IRepository $repository)
	{
		$this->checkEvent = 'onAfterDelete';
	}

	/**
	 * Persistovane zmeny (update)
	 * @param IRepository
	 */
	protected function onBeforeUpdate(IRepository $repository)
	{
		$this->checkEvent = 'onBeforeUpdate';
	}

	/**
	 * Persistovane zmeny (update)
	 * @param IRepository
	 */
	protected function onAfterUpdate(IRepository $repository)
	{
		$this->checkEvent = 'onAfterUpdate';
	}

	/**
	 * Persistovane zmeny (insert)
	 * @param IRepository
	 */
	protected function onBeforeInsert(IRepository $repository)
	{
		$this->checkEvent = 'onBeforeInsert';
	}

	/**
	 * Persistovane zmeny (insert)
	 * @param IRepository
	 */
	protected function onAfterInsert(IRepository $repository)
	{
		$this->checkEvent = 'onAfterInsert';
	}

	/**
	 * Do not call directly!
	 * Vola urcitou udalost.
	 * @internal
	 * @param IEntity
	 * @param string nazev udalosti
	 * @param IRepository
	 * @param array|int $data (onLoad) or $id (onPersist)
	 */
	final public static function ___event(IEntity $entity, $event, IRepository $repository = NULL, $more = NULL)
	{
		$method = 'on' . ucfirst($event);
		$entity->checkEvent = NULL;
		if ($more === NULL)
		{
			$entity->{$method}($repository);
		}
		else
		{
			$entity->{$method}($repository, $more);
		}

		if ($entity->checkEvent !== $method)
		{
			$class = get_class($entity);
			throw new InvalidStateException("Method $class::$method() or its descendant doesn't call parent::$method().");
		}
	}

}