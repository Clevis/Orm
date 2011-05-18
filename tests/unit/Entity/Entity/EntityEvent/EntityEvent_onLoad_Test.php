<?php

require_once dirname(__FILE__) . '/../../../../boot.php';

/**
 * @covers Orm\_EntityEvent::onLoad
 */
class EntityEvent_onLoad_Test extends EntityEvent_event_Base
{

	public function testGetById()
	{
		$e = $this->r->getById(1);
		$this->assertSame('onLoad', $e->event);
		$this->assertSame(array($this->r, array('id' => 1)), $e->eventParam);
	}

}
