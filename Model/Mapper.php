<?php

abstract class Mapper extends Object implements IMapper
{
	abstract public function findAll();
	abstract protected function findBy(array $where);
	abstract protected function getBy(array $where);
	abstract public function persist(Entity $e);
	
	protected $repository;
	
	public function __construct(Repository $repository)
	{
		$this->repository = $repository;
	}
	
	public function __call($name, $args)
	{
		try {
			return parent::__call($name, $args);
		} catch (MemberAccessException $e) {
		
			$mode = $by = NULL;
			if (substr($name, 0, 6) === 'findBy')
			{
				$mode = 'find';
				$by = substr($name, 6);
			}
			else if (substr($name, 0, 5) === 'getBy')
			{
				$mode = 'get';
				$by = substr($name, 5);
			}
			
			if ($mode AND $by)
			{
				$where = array();
				foreach (array_map('ucfirst',explode('And', $by)) as $n => $key)
				{
					if (!array_key_exists($n, $args)) throw new InvalidArgumentException("There is no value for '$key'.");
					$where[$key] = $args[$n];
				}
				return $mode === 'get' ? $this->getBy($where) : $this->findBy($where);
			}
		
			throw $e;
		}
	}
	
	protected function apply($data)
	{
		if ($data instanceof a)
		{
			return $this->repository->createEntity($data);
		}
		else
		{
			if (!($data instanceof DibiResult))
			{
				return $data->getResult();
			}
			return new EntityCollection($this->repository, $data->setRowClass('a'));
		}
	}

	
	
}