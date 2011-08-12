<?php

use Nette\Utils\Html;

/**
 * @covers Orm\ManyToMany::get
 */
class ManyToMany_get_Test extends ManyToMany_Test
{

	public function test()
	{
		$this->assertInstanceOf('Orm\IEntityCollection', $this->m2m->get());
	}

	public function testClone()
	{
		$this->assertNotSame($this->m2m->get(), $this->m2m->get());
	}


	public function testCloneHasResult()
	{
		$cc = $this->m2m->_getCollection();
		$this->assertInstanceOf('Orm\ArrayCollection', $cc);

		$all = $cc->fetchAll();
		$r = setAccessible(new ReflectionProperty('Orm\ArrayCollection', 'result'));
		$html = array(new Html, new Html);
		$r->setValue($cc, $html);

		$c = $this->m2m->get();
		$this->assertInstanceOf('Orm\ArrayCollection', $c);

		$this->assertAttributeSame($html, 'source', $c);
		$this->assertAttributeSame($html, 'result', $cc);
	}

	public function testCloneHasResultLate()
	{
		$cc = $this->m2m->_getCollection();
		$this->assertInstanceOf('Orm\ArrayCollection', $cc);

		$c = $this->m2m->get();
		$this->assertInstanceOf('Orm\ArrayCollection', $c);

		$all = $cc->fetchAll();
		$r = setAccessible(new ReflectionProperty('Orm\ArrayCollection', 'result'));
		$html = array(new Html, new Html);
		$r->setValue($cc, $html);

		$this->assertAttributeSame($all, 'source', $c);
		$this->assertAttributeSame($html, 'result', $cc);
	}

	public function testSameData()
	{
		$ids1 = $this->m2m->get()->fetchPairs('id', 'id');
		$ids2 = $this->m2m->_getCollection()->fetchPairs('id', 'id');
		$this->assertSame($ids2, $ids1);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\BaseToMany', 'get');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}