<?php

namespace Orm;

use Nette\Object;
use Nette\NotImplementedException;
use Dibi;
use InvalidArgumentException;
use DateTime;
use ArrayIterator;
use Nette\DeprecatedException;

require_once dirname(__FILE__) . '/IEntityCollection.php';
require_once dirname(__FILE__) . '/Helpers/FetchAssoc.php';
require_once dirname(__FILE__) . '/Helpers/FindByHelper.php';

class ArrayCollection extends Object implements IEntityCollection, ArrayDataSource
{
	protected $source;

	/** @var array */
	protected $result;

	/** @var int */
	protected $count;

	/** @var int */
	protected $totalCount;

	/** @var array */
	protected $cols = array();

	/** @var array */
	protected $sorting = array();

	/** @var array */
	protected $conds = array();

	/** @var int */
	protected $offset;

	/** @var int */
	protected $limit;



	final public function __construct(array $source)
	{
		$tmp = array();
		foreach ($source as $entity) $tmp[spl_object_hash($entity)] = $entity;
		$this->source = array_values($tmp);
	}



	/**
	 * Selects columns to query.
	 * @param  string|array  column name or array of column names
	 * @param  string  		 column alias
	 * @return ArrayCollection  provides a fluent interface
	 */
	final public function select($col, $as = NULL)
	{
		throw new NotImplementedException();
		if (is_array($col)) {
			$this->cols = $col;
		} else {
			$this->cols[$col] = $as;
		}
		$this->result = NULL;
		return $this;
	}



	/**
	 * Adds conditions to query.
	 * @param  mixed  conditions
	 * @return ArrayCollection  provides a fluent interface
	 */
	final public function where($cond)
	{
		if (is_array($cond)) {
			// TODO: not consistent with select and orderBy
			$this->conds[] = $cond;
		} else {
			$this->conds[] = func_get_args();
		}
		$this->result = $this->count = NULL;
		return $this;
	}



	/**
	 * Selects columns to order by.
	 * @param  string|array  column name or array of column names
	 * @param  string  		 sorting direction
	 * @return ArrayCollection  provides a fluent interface
	 */
	final public function orderBy($row, $direction = Dibi::ASC)
	{
		if (is_array($row))
		{
			$this->sorting = array();
			foreach ($row as $name => $direction)
			{
				$this->orderBy((string) $name, $direction);
			}
		}
		else
		{
			$direction = strtoupper($direction);
			if ($direction !== Dibi::ASC AND $direction !== Dibi::DESC)
			{
				if ($direction === false OR $direction === NULL) $direction = Dibi::ASC;
				else if ($direction === true) $direction = Dibi::DESC;
				else $direction = Dibi::ASC;
			}

			$this->sorting[] = array($row, $direction);
		}
		$this->result = NULL;
		return $this;
	}



	/**
	 * Limits number of rows.
	 * @param  int limit
	 * @param  int offset
	 * @return ArrayCollection  provides a fluent interface
	 */
	final public function applyLimit($limit, $offset = NULL)
	{
		$this->limit = $limit;
		$this->offset = $offset;
		$this->result = $this->count = NULL;
		return $this;
	}


	/********************* executing ****************d*g**/

	private $_sort;
	private function _sort($aRow, $bRow)
	{
		foreach ($this->_sort as $tmp)
		{
			$key = $tmp[0];
			$direction = $tmp[1];
			if (strpos($key, '->') !== false)
			{
				$a = $aRow;
				$b = $bRow;
				foreach (explode('->', $key) as $k)
				{
					if (!($a instanceof IEntity)) $a = NULL;
					else if (!$a->hasParam($k))
					{
						throw new InvalidArgumentException("'$k' is not key in '{$key}'");
					}
					else $a = $a->{$k};

					if (!($b instanceof IEntity)) $b = NULL;
					else if (!$b->hasParam($k))
					{
						throw new InvalidArgumentException("'$k' is not key in '{$key}'");
					}
					else $b = $b->{$k};
				}
			}
			else
			{
				if (!$aRow->hasParam($key) OR !$bRow->hasParam($key))
				{
					throw new InvalidArgumentException("'$key' is not key");
				}
				if (!isset($aRow->{$key}) OR !isset($bRow->{$key}))
				{
					throw new InvalidArgumentException("'$key' is not key");
				}

				$a = $aRow->{$key};
				$b = $bRow->{$key};
			}

			if (is_scalar($a) AND is_scalar($b))
			{
				$r = strnatcasecmp($a, $b);
			}
			else if ($a instanceof DateTime AND $b instanceof DateTime)
			{
				$r = $a < $b ? -1 : 1;
			}
			else if ($b === NULL)
			{
				$r = 1;
			}
			else if ($a === NULL)
			{
				$r = -1;
			}
			else
			{
				throw new InvalidArgumentException("'$key' is not sortable key");
			}

			if ($r !== 0)
			{
				break;
			}
		}

		if ($direction === Dibi::DESC) return -$r;
		return $r;
	}
	/**
	 * Returns (and queries) DibiResult.
	 * @return DibiResult
	 */
	final public function getResult()
	{
		if ($this->result === NULL)
		{
			$source = $this->source;

			if ($this->conds)
			{
				// todo
				if (count($this->conds) === 1 AND count($this->conds[0]) === 2 AND preg_match('#^\s*\[id\]\s*\=\s*\%[i|s]\s*$#',$this->conds[0][0]) AND is_scalar($this->conds[0][1]))
				{
					$copySource = $source;
					$source = array();
					foreach ($copySource as $row)
					{
						if ($row['id'] == $this->conds[0][1])
						{
							$source[] = $row;
						}
					}
				}
				else
				{
					throw new NotImplementedException();
				}
			}

			if ($this->sorting)
			{
				$this->_sort = $this->sorting;
				$this->_sort[] = array('id', Dibi::ASC);
				uasort($source, array($this, '_sort'));
				$this->_sort = NULL;
			}

			if ($this->offset !== NULL OR $this->limit !== NULL)
			{
				$source = array_slice($source, (int) $this->offset, $this->limit);
			}

			$this->result = $source;
		}
		return $this->result;
	}



	/**
	 * @return DibiResultIterator
	 */
	final public function getIterator()
	{
		return new ArrayIterator($this->getResult());
	}



	/**
	 * Generates, executes SQL query and fetches the single row.
	 * @return DibiRow|FALSE  array on success, FALSE if no next record
	 */
	final public function fetch()
	{
		$row = current($this->getResult());
		return $row === false ? NULL : $row;
	}



	/**
	 * Like fetch(), but returns only first field.
	 * @return mixed  value on success, FALSE if no next record
	 */
	final public function fetchSingle()
	{
		throw new NotImplementedException();
		return $this->getResult()->fetchSingle();
	}



	/**
	 * Fetches all records from table.
	 * @return array
	 */
	final public function fetchAll()
	{
		return $this->getResult();
	}



	/**
	 * Fetches all records from table and returns associative tree.
	 * @param  string  associative descriptor
	 * @return array
	 */
	final public function fetchAssoc($assoc)
	{
		return FetchAssoc::apply($this->fetchAll(), $assoc);
	}



	/**
	 * Fetches all records from table like $key => $value pairs.
	 * @param  string  associative key
	 * @param  string  value
	 * @return array
	 */
	final public function fetchPairs($key = NULL, $value = NULL)
	{
		$row = $this->fetch();
		if (!$row) return array();  // empty result set

		$data = array();

		if ($value === NULL) {
			if ($key !== NULL) {
				throw new InvalidArgumentException("Either none or both columns must be specified.");
			}

			// autodetect
			$tmp = array_keys($row->toArray());
			$key = $tmp[0];
			if (count($row) < 2) { // indexed-array
				foreach ($this->getResult() as $row)
				{
					$data[] = $row[$key];
				}
				return $data;
			}

			$value = $tmp[1];

		} else {
			if (!$row->hasParam($value)) {
				throw new InvalidArgumentException("Unknown value column '$value'.");
			}

			if ($key === NULL) { // indexed-array
				foreach ($this->getResult() as $row)
				{
					$data[] = $row[$value];
				}
				return $data;
			}

			if (!$row->hasParam($key)) {
				throw new InvalidArgumentException("Unknown key column '$key'.");
			}
		}

		foreach ($this->getResult() as $row)
		{
			$data[ $row[$key] ] = $row[$value];
		}

		return $data;
	}

	/**
	 * Returns the number of rows in a given data source.
	 * @return int
	 */
	final public function count()
	{
		return count($this->getResult());
	}

	/**
	 * Returns the number of rows in a given data source.
	 * @return int
	 */
	final public function getTotalCount()
	{
		return count($this->data);
	}

	/** @return ArrayCollection */
	final public function toArrayCollection()
	{
		return new ArrayCollection($this->getResult());
	}

	/** @return ArrayCollection */
	final public function toCollection()
	{
		$class = get_class($this);
		return new $class($this->getResult());
	}

	final public function findBy(array $where)
	{
		foreach ($where as $key => $value)
		{
			if (is_array($value))
			{
				$value = array_unique(
					array_map(
						create_function('$v', 'return $v instanceof Orm\IEntity ? $v->id : $v;'),
						$value
					)
				);
				$where[$key] = $value;
			}
			else if ($value instanceof IEntityCollection)
			{
				$where[$key] = $value->fetchPairs(NULL, 'id');
			}
			else if ($value instanceof IEntity)
			{
				$value = isset($value->id) ? $value->id : NULL;
				$where[$key] = $value;
			}
		}

		$all = $this->getResult();
		$result = array();
		foreach ($all as $entity)
		{
			$equal = false;
			foreach ($where as $key => $value)
			{
				$eValue = $entity[$key];
				$eValue = $eValue instanceof IEntity ? (isset($eValue->id) ? $eValue->id : NULL) : $eValue;

				if ($eValue == $value OR (is_array($value) AND in_array($eValue, $value)))
				{
					$equal = true;
				}
				else
				{
					$equal = false;
					break;
				}
			}
			if ($equal)
			{
				$result[] = $entity;
			}
		}

		return new ArrayCollection($result);
	}

	final public function getBy(array $where)
	{
		return $this->findBy($where)->applyLimit(1)->fetch();
	}

	final public function __call($name, $args)
	{
		if (!method_exists($this, $name) AND FindByHelper::parse($name, $args))
		{
			return $this->$name($args);
		}
		return parent::__call($name, $args);
	}

	/** @deprecated */
	final public function toArrayDataSource(){throw new DeprecatedException('Use Orm\ArrayCollection::toArrayCollection() instead');}
	/** @deprecated */
	final public function toDataSource(){throw new DeprecatedException('Use Orm\ArrayCollection::toCollection() instead');}
}