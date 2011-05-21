<?php

require_once dirname(__FILE__) . '/../../../../boot.php';

/**
 * @covers Orm\DibiCollection::where
 */
class DibiCollection_where_Test extends DibiCollection_Base_Test
{

	public function test()
	{
		$this->c->where('1=1');
		$this->assertAttributeSame(array(array('1=1')), 'where', $this->c);
		$this->c->where('2=2');
		$this->assertAttributeSame(array(array('1=1'), array('2=2')), 'where', $this->c);
	}

	public function testArray()
	{
		$this->c->where(array('[bb] = [aa]'), 'lost');
		$this->assertAttributeSame(array(array('[bb] = [aa]')), 'where', $this->c);
	}

	public function testMoreParams()
	{
		$this->c->where('%n = %s', 'foo', 'bar');
		$this->assertAttributeSame(array(array('%n = %s', 'foo', 'bar')), 'where', $this->c);
	}

	public function testWipe()
	{
		DibiCollection_DibiCollection::set($this->c, 'result', array());
		DibiCollection_DibiCollection::set($this->c, 'count', 666);
		$this->assertAttributeSame(array(), 'result', $this->c);
		$this->assertAttributeSame(666, 'count', $this->c);
		$this->c->where('1=1');
		$this->assertAttributeSame(NULL, 'result', $this->c);
		$this->assertAttributeSame(NULL, 'count', $this->c);
	}

	public function testReturns()
	{
		$this->assertSame($this->c, $this->c->where('1=1'));
	}

}