<?php

/**
 * @covers Orm\DibiCollection::fetch
 */
class DibiCollection_fetch_Test extends DibiCollection_BaseConnected_Test
{

	public function testOk()
	{
		$this->e(1, false);
		$e = $this->c->fetch();
		$this->assertInstanceOf('TestEntity', $e);
		$this->assertSame(1, $e->id);
		$this->assertSame('boo', $e->string);
	}

	public function testNoRow()
	{
		$this->e(0);
		$e = $this->c->fetch();
		$this->assertSame(NULL, $e);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\BaseDibiCollection', 'fetch');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
