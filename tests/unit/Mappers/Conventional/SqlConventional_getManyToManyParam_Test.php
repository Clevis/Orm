<?php

require_once dirname(__FILE__) . '/../../../boot.php';

/**
 * @covers Orm\SqlConventional::getManyToManyParam
 * @covers Orm\SqlConventional::foreignKeyFormat
 */
class SqlConventional_getManyToManyParam_Test extends TestCase
{
	private $c;
	protected function setUp()
	{
		$this->c = new MockSqlConventional;
	}

	public function test()
	{
		$this->assertSame('xxx_id', $this->c->getManyToManyParam('xxx'));
		$this->assertSame('same_thing_id', $this->c->getManyToManyParam('sameThing'));
		$this->assertSame('same_thing_id', $this->c->getManyToManyParam('same_thing'));
		$this->assertSame('same1_thing_id', $this->c->getManyToManyParam('same1Thing'));
		$this->assertSame('a_b_c_id', $this->c->getManyToManyParam('ABC'));
		$this->assertSame('123_id', $this->c->getManyToManyParam('123'));
		$this->assertSame('same_thing_same_thing_id', $this->c->getManyToManyParam('sameThingSameThing'));
	}

}