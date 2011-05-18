<?php

require_once dirname(__FILE__) . '/../../../../boot.php';

/**
 * @covers DibiCollection::join
 * @covers DibiMapper::getJoinInfo
 */
class DibiCollection_join_ManyToMany_Test extends TestCase
{
	/** @var DibiCollection_join_ManyToMany1_Repository */
	private $r1;
	/** @var DibiCollection_join_ManyToMany2_Repository */
	private $r2;
	/** @var DibiCollection */
	private $c;

	private function a($expectedSql, DibiCollection $c)
	{
		$csql = $c->__toString();
		$trimcsql = trim(preg_replace('#\s+#', ' ', $csql));
		$trimsql = trim(preg_replace('#\s+#', ' ', $expectedSql));
		$this->assertSame($trimsql, $trimcsql, "$expectedSql\n\n$csql");
	}

	protected function setUp()
	{
		$model = new RepositoryContainer;
		$this->r1 = $model->dibiCollection_join_ManyToMany1_;
		$this->r2 = $model->dibiCollection_join_ManyToMany2_;
		$this->c = $this->r1->mapper->findAll();
	}

	public function testOneTable()
	{
		$this->a('
			SELECT [e].* FROM [dibicollection_join_manytomany1_] as e
			LEFT JOIN [mm] as [m2m__joins] ON [m2m__joins].[first_id] = [e].[id]
			LEFT JOIN [dibicollection_join_manytomany2_] as [joins] ON [joins].[id] = [m2m__joins].[second_id]
			GROUP BY [e].[id]
			ORDER BY [joins].[name] ASC
		', $this->c->orderBy('joins->name'));
	}

	public function testOverTwoTable()
	{
		$this->a('
			SELECT [e].* FROM [dibicollection_join_manytomany1_] as e
			LEFT JOIN [mm] as [m2m__joins] ON [m2m__joins].[first_id] = [e].[id]
			LEFT JOIN [dibicollection_join_manytomany2_] as [joins] ON [joins].[id] = [m2m__joins].[second_id]
			LEFT JOIN [mm] as [joins->m2m__joins] ON [joins->m2m__joins].[second_id] = [joins].[id]
			LEFT JOIN [dibicollection_join_manytomany1_] as [joins->joins] ON [joins->joins].[id] = [joins->m2m__joins].[first_id]
			GROUP BY [e].[id]
			ORDER BY [joins->joins].[name] ASC
		', $this->c->orderBy('joins->joins->name'));
	}

	public function testTwoJoin()
	{
		$this->a('
			SELECT [e].* FROM [dibicollection_join_manytomany1_] as e
			LEFT JOIN [mm] as [m2m__joins] ON [m2m__joins].[first_id] = [e].[id]
			LEFT JOIN [dibicollection_join_manytomany2_] as [joins] ON [joins].[id] = [m2m__joins].[second_id]
			LEFT JOIN [mm] as [joins->m2m__joins] ON [joins->m2m__joins].[second_id] = [joins].[id]
			LEFT JOIN [dibicollection_join_manytomany1_] as [joins->joins] ON [joins->joins].[id] = [joins->m2m__joins].[first_id]
			GROUP BY [e].[id]
			ORDER BY [joins->joins].[name] ASC, [joins].[name] ASC
		', $this->c->orderBy('joins->joins->name')->orderBy('joins->name'));
	}

}
