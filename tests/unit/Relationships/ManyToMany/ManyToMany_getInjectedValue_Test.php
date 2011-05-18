<?php

require_once dirname(__FILE__) . '/../../../boot.php';

/**
 * @covers Orm\ManyToMany::getInjectedValue
 * @covers Orm\ArrayManyToManyMapper::getValue
 */
class ManyToMany_getInjectedValue_Test extends ManyToMany_Test
{

	public function test()
	{
		$this->assertSame(array(10=>10,11=>11,12=>12,13=>13), $this->m2m->getInjectedValue());
	}

}