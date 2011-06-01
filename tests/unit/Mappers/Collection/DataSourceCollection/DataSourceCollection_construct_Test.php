<?php

require_once dirname(__FILE__) . '/../../../../boot.php';

/**
 * @covers Orm\DataSourceCollection::__construct
 * @covers Orm\BaseDibiCollection::__construct
 */
class DataSourceCollection_construct_Test extends DataSourceCollection_Base_Test
{

	public function test()
	{
		$this->assertAttributeSame('datasourcecollection', 'sql', $this->c);
		$this->assertAttributeSame($this->m->repository, 'repository', $this->c);
		$this->assertAttributeSame($this->m->connection, 'connection', $this->c);
		$this->assertAttributeSame($this->m->repository->mapper->conventional, 'conventional', $this->c);
	}

	public function testSql()
	{
		$this->a('SELECT * FROM `datasourcecollection`');
	}

}