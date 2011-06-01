<?php

use Orm\PerformanceHelper;

require_once dirname(__FILE__) . '/../../../boot.php';

/**
 * @covers Orm\PerformanceHelper::getDefaultKey
 */
class PerformanceHelper_getDefaultKey_Test extends TestCase
{

	public function test()
	{
		$k = PerformanceHelper::getDefaultKey();
		$this->assertNotEmpty($k);
		$this->assertSame($_SERVER['REQUEST_URI'], $k);
	}

	public function test2()
	{
		$tmp = $_SERVER['REQUEST_URI'];
		unset($_SERVER['REQUEST_URI']);
		$k = PerformanceHelper::getDefaultKey();
		$this->assertSame(NULL, $k);
		$_SERVER['REQUEST_URI'] = $tmp;
	}

}