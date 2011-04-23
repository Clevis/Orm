<?php

require_once __DIR__ . '/../../../../boot.php';

/**
 * @covers _EntityValue::onCreate
 */
class EntityValue_onCreate_Test extends TestCase
{
	private $e;

	protected function setUp()
	{
		$this->e = new TestEntity;
	}

	public function test()
	{
		$this->assertTrue($this->e->isChanged());
		$this->assertInternalType('array', $this->readAttribute($this->e, 'rules'));
	}

}