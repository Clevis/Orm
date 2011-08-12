<?php

use Nette\Utils\Html;
use Orm\MetaData;

/**
 * @covers Orm\MetaData::getEntityRules
 * @covers Orm\MetaData::createEntityRules
 */
class MetaData_getEntityRules_NoRepoCon_Test extends TestCase
{
	protected function setUp()
	{
		MetaData::clean();
		MetaData_Test_Entity::$metaData = NULL;
	}

	public function testCache()
	{
		MetaData_Test_Entity::$count = 0;
		MetaData::getEntityRules('MeTaData_Test_Entity');
		MetaData::getEntityRules('MeTaData_Test_Entity');
		MetaData::getEntityRules('MeTaData_Test_Entity');
		$this->assertSame(1, MetaData_Test_Entity::$count);
	}

	public function testNotExists()
	{
		$this->setExpectedException('Nette\InvalidStateException', "Class 'Xxxasdsad' doesn`t exists");
		MetaData::getEntityRules('Xxxasdsad');
	}

	public function testNotEntity()
	{
		$this->setExpectedException('Nette\InvalidStateException', "'Nette\\Utils\\Html' isn`t instance of Orm\\IEntity");
		MetaData::getEntityRules('Nette\Utils\Html');
	}

	public function testBadReturn()
	{
		$this->setExpectedException('Orm\BadReturnException', "MetaData_Test_Entity::createMetaData() must return Orm\\MetaData, 'Nette\\Utils\\Html' given.");
		MetaData_Test_Entity::$metaData = new Html;
		MetaData::getEntityRules('MetaData_Test_Entity');
	}

	public function testReturn()
	{
		$this->assertInternalType('array', MetaData::getEntityRules('MetaData_Test_Entity'));
	}

	public function testRecursionCache()
	{
		$this->assertAttributeEmpty('cache2', 'Orm\MetaData');
		MetaData::getEntityRules('RelationshipLoader_ManyToMany1_Entity');
		$this->assertAttributeEmpty('cache2', 'Orm\MetaData');
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\MetaData', 'getEntityRules');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertTrue($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}