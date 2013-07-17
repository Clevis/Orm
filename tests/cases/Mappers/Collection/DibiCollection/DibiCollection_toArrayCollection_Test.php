<?php

/**
 * @covers Orm\DibiCollection::toArrayCollection
 */
class DibiCollection_toArrayCollection_Test extends DibiCollection_BaseConnected_Test
{

	public function testOk()
	{
		$this->e(3);
		$a = $this->c->toArrayCollection();
		$this->assertInstanceOf('Orm\ArrayCollection', $a);
		$this->assertSame(3, count($a));

		$this->assertSame($a->fetchAll(), $this->c->fetchAll());
	}

	public function testNoRow()
	{
		$this->e(0);
		$this->assertSame(0, $this->c->toArrayCollection()->count());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\BaseDibiCollection', 'toArrayCollection');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
