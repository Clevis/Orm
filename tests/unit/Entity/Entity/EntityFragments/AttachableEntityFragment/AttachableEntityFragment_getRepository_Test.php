<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\AttachableEntityFragment::getRepository
 */
class AttachableEntityFragment_getRepository_Test extends TestCase
{
	private $r;

	protected function setUp()
	{
		$m = new RepositoryContainer;
		$this->r = $m->testentity;
	}

	public function testNotNeed()
	{
		$e = new TestEntity;
		$this->assertSame(NULL, $e->getRepository(false));
		$e = $this->r->getById(1);
		$this->assertSame($this->r, $e->getRepository(false));
	}

	public function testNeed1()
	{
		$e = $this->r->getById(1);
		$this->assertSame($this->r, $e->getRepository(true));
		$this->assertSame($this->r, $e->getRepository());
	}

	public function testNeed2()
	{
		$e = new TestEntity;
		$this->setExpectedException('Nette\InvalidStateException', 'TestEntity is not attached to repository.');
		$e->getRepository(true);
	}

}
