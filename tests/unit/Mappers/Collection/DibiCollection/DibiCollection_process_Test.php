<?php

require_once dirname(__FILE__) . '/../../../../boot.php';

/**
 * @covers Orm\DibiCollection::process
 * @see DibiCollection_toString_applyLimit_Test
 * @see DibiCollection_toString_orderBy_Test
 * @see DibiCollection_toCollection_Test
 */
class DibiCollection_process_Test extends DibiCollection_Base_Test
{

	public function test()
	{
		$this->c->applyLimit(10, 20);
		$this->c->orderBy('xyz');
		$r = DibiCollection_DibiCollection::call($this->c, 'process');
		$this->assertSame(3, count($r));
		$this->assertSame(array(array('e.xyz', Dibi::ASC)), $r[0]);
		$this->assertSame(10, $r[1]);
		$this->assertSame(20, $r[2]);
	}

}