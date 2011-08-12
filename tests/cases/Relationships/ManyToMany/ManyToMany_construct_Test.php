<?php

/**
 * @covers Orm\ManyToMany::__construct
 * @covers Orm\BaseToMany::__construct
 */
class ManyToMany_construct_Test extends ManyToMany_Test
{

	public function testWithRepoName()
	{
		$this->m2m = new ManyToMany_ManyToMany($this->e, get_class($this->r), 'param', 'param', true, array(10,11,12,13));
		$this->t(10,11,12,13);
	}

	public function testWithRepoObject()
	{
		$this->m2m = new ManyToMany_ManyToMany($this->e, $this->r, 'param', 'param', true, array(10,11,12,13));
		$this->t(10,11,12,13);
	}

	public function testBadRepo()
	{
		$this->m2m = new ManyToMany_ManyToMany($this->e, 'unexists', 'param', 'param', true);
		$this->setExpectedException('Orm\RepositoryNotFoundException', "Repository 'unexists' doesn't exists");
		$this->m2m->_getCollection();
	}

	public function testNoPersistedEntity_repo()
	{
		$this->m2m = new ManyToMany_ManyToMany(new TestEntity, $this->r, 'param', 'param', true);
		$this->assertInstanceOf('Orm\ArrayCollection', $this->m2m->_getCollection());
		$this->t();
	}

	public function testNoPersistedEntity_repoName()
	{
		$this->m2m = new ManyToMany_ManyToMany(new TestEntity, get_class($this->r), 'param', 'param', true);
		$this->assertInstanceOf('Orm\ArrayCollection', $this->m2m->_getCollection());
		$this->t();
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ManyToMany', '__construct');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}