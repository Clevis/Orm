<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\_EntityGeneratingRepository::onAttach
 */
class EntityGeneratingRepository_onPersist_Test extends TestCase
{
	private $r;

	protected function setUp()
	{
		$m = new RepositoryContainer;
		$this->r = $m->testentity;
	}

	public function test()
	{
		$e = new TestEntity;
		$this->assertSame(NULL, $e->getGeneratingRepository(false));
		$this->r->persist($e);
		$this->assertSame($this->r, $e->getGeneratingRepository(false));
	}

}