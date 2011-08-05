<?php

use Nette\Utils\Html;
use Orm\RepositoryContainer;

/**
 * @covers Orm\ArrayMapper::getConventional
 */
class ArrayMapper_getConventional_Test extends TestCase
{
	private $m;
	protected function setUp()
	{
		$this->m = new ArrayMapper_getConventional_ArrayMapper(new TestsRepository(new RepositoryContainer));
	}

	public function test()
	{
		$this->assertInstanceOf('Orm\IConventional', $this->m->getConventional());
	}

	public function test2()
	{
		$this->assertSame($this->m->getConventional(), $this->m->getConventional());
	}

	public function testBad()
	{
		$this->m->c = new Html;
		$this->setExpectedException('Nette\InvalidStateException', 'ArrayMapper_getConventional_ArrayMapper::createConventional() must return Orm\IConventional');
		$this->m->getConventional();
	}

	public function testJustIConventional()
	{
		$this->m->c = new Mapper_getConventional_Conventional;
		$this->assertInstanceOf('Mapper_getConventional_Conventional', $this->m->getConventional());
	}

}
