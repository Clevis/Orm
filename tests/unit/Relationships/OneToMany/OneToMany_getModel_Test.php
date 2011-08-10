<?php

/**
 * @covers Orm\OneToMany::getModel
 */
class OneToMany_getModel_Test extends OneToMany_Test
{

	public function test()
	{
		$this->assertSame($this->e->getModel(), $this->o2m->getModel());
	}

	public function testNotHas()
	{
		$this->e->___event($this->e, 'afterRemove', $this->e->repository);
		$this->setExpectedException('Nette\InvalidStateException', 'TestEntity is not attached to repository.');
		$this->o2m->getModel();
	}

	public function testDontNeedHas()
	{
		$this->assertSame($this->e->getModel(), $this->o2m->getModel(false));
	}

	public function testDontNeedNotHas()
	{
		$this->e->___event($this->e, 'afterRemove', $this->e->repository);
		$this->assertSame(NULL, $this->o2m->getModel(false));
	}

	public function testDontNeedNotHasNull()
	{
		$this->e->___event($this->e, 'afterRemove', $this->e->repository);
		$this->assertSame(NULL, $this->o2m->getModel(NULL));
	}

	public function testNeed()
	{
		$this->assertSame($this->e->getModel(), $this->o2m->getModel(true));
	}

	public function testNeedNotHas()
	{
		$this->e->___event($this->e, 'afterRemove', $this->e->repository);
		$this->setExpectedException('Nette\InvalidStateException', 'TestEntity is not attached to repository.');
		$this->o2m->getModel(true);
	}
}
