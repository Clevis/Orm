<?php

namespace Orm;

use Countable;
use IteratorAggregate;
use Dibi;

interface IEntityCollection extends Countable, IteratorAggregate
{

	/**
	 * Selects columns to order by.
	 * @param string|array column name or array of column names
	 * @param string sorting direction Dibi::ASC or Dibi::DESC
	 * @return IEntityCollection $this
	 */
	public function orderBy($row, $direction = Dibi::ASC);

	/**
	 * Limits number of rows.
	 * @param int
	 * @param int
	 * @return IEntityCollection $this
	 */
	public function applyLimit($limit, $offset = NULL);

	/**
	 * Fetches the single row.
	 * @return IEntity|NULL
	 */
	public function fetch();

	/**
	 * Fetches all records.
	 * @return array of IEntity
	 */
	public function fetchAll();

	/**
	 * Fetches all records and returns associative tree.
	 * @param string associative descriptor
	 * @return array
	 */
	public function fetchAssoc($assoc);

	/**
	 * Fetches all records like $key => $value pairs.
	 * @param string associative key
	 * @param string value
	 * @return array
	 */
	public function fetchPairs($key = NULL, $value = NULL);

	/**
	 * Vraci kolekci entit dle kriterii.
	 * @param array
	 * @return IEntityCollection
	 */
	public function findBy(array $where);

	/**
	 * Vraci jednu entitu dle kriterii.
	 * @param array
	 * @return IEntity|NULL
	 */
	public function getBy(array $where);

	/** @return ArrayCollection */
	public function toArrayCollection();

	/** @return IEntityCollection */
	public function toCollection();

}
