<?php

use Orm\InjectionFactory;
use Orm\IEntity;

/**
 * @covers Orm\Injection::create
 */
class Injection_create_Test extends TestCase
{

	public function testInterface()
	{
		$i = new Injection_create_Injection;
		$this->assertInstanceOf('Orm\IEntityInjection', $i);
		$this->assertInstanceOf('Orm\IEntityInjectionStaticLoader', $i);
	}

	public function testOk()
	{
		$i1 = Injection_create_Injection::create('Injection_create_Injection', new TestEntity, 'value');
		$this->assertInstanceOf('Orm\Injection', $i1);
		$this->assertAttributeSame('value', 'value', $i1);
		$i2 = Injection_create_Injection::create('Injection_create_Injection', new TestEntity, NULL);
		$this->assertInstanceOf('Orm\Injection', $i2);
		$this->assertAttributeSame(NULL, 'value', $i2);
		$this->assertNotSame($i1, $i2);
	}

	public function testBadClass()
	{
		$this->setExpectedException('Nette\InvalidArgumentException', "Directory is't subclass of Orm\\Injection");
		Injection_create_Injection::create('Directory', new TestEntity, NULL);
	}

	public function testHasConstructor()
	{
		$i = Injection_create_Injection_Constructor::create('Injection_create_Injection_Constructor', new TestEntity, NULL);
		$this->assertInstanceOf('Injection_create_Injection_Constructor', $i);
	}

	public function testHasConstructorWithParams()
	{
		$this->setExpectedException('Nette\InvalidStateException', "Injection_create_Injection_ConstructorWithParams has required parameters in constructor, use custom factory");
		Injection_create_Injection_ConstructorWithParams::create('Injection_create_Injection_ConstructorWithParams', new TestEntity, NULL);
	}

	public function testHasConstructorWithParamsNotRequired()
	{
		$i = Injection_create_Injection_ConstructorWithParamsNotRequired::create('Injection_create_Injection_ConstructorWithParamsNotRequired', new TestEntity, NULL);
		$this->assertInstanceOf('Injection_create_Injection_ConstructorWithParamsNotRequired', $i);
	}

	public function testInitialValue()
	{
		$i = Injection_create_Injection::create('Injection_create_Injection', new TestEntity, 'value');
		$this->assertAttributeSame('value', 'value', $i);
	}

}
