<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/db/ComplexSelectSQL.class.php');

class ComplexSelectSQLTest extends LimbTestCase
{
  function ComplexSelectSQLTest()
  {
    parent :: LimbTestCase('complex select sql tests');
  }

  function testSelect()
  {
    $sql = new ComplexSelectSQL('SELECT * FROM test');

    $this->assertEqual($sql->toString(), 'SELECT * FROM test');
  }

  function testNoFields()
  {
    $sql = new ComplexSelectSQL('SELECT %fields% FROM test');

    $this->assertEqual($sql->toString(), 'SELECT * FROM test');
  }

  function testAddFieldWithFields()
  {
    $sql = new ComplexSelectSQL('SELECT t3 %fields%,t4 FROM test');

    $sql->addField('t1');
    $sql->addField('t2');

    $this->assertEqual($sql->toString(), 'SELECT t3 ,t1,t2,t4 FROM test');
  }

  function testNoFieldsAdded()
  {
    $sql = new ComplexSelectSQL('SELECT t3 %fields%,t4 FROM test');

    $this->assertEqual($sql->toString(), 'SELECT t3 ,t4 FROM test');
  }

  function testAddFieldNoFields()
  {
    $sql = new ComplexSelectSQL('SELECT %fields% FROM test');

    $sql->addField('t1');
    $sql->addField('t2');

    $this->assertEqual($sql->toString(), 'SELECT t1,t2 FROM test');
  }

  function testNoJoin()
  {
    $sql = new ComplexSelectSQL('SELECT * FROM test %join%');

    $expected = 'SELECT * FROM test';

    $this->assertEqual($sql->toString(), $expected);
  }

  function testAddJoin()
  {
    $sql = new ComplexSelectSQL('SELECT * FROM test %join%');

    $sql->addJoin('article', array('test.article_id' => 'article.id'));

    $expected = 'SELECT * FROM test LEFT JOIN article ON test.article_id=article.id';

    $this->assertEqual($sql->toString(), $expected);
  }

  function testEmptyCondition()
  {
    $sql = new ComplexSelectSQL('SELECT * FROM test %where%');

    $this->assertEqual($sql->toString(),
                       'SELECT * FROM test');
  }

  function testAddCondition()
  {
    $sql = new ComplexSelectSQL('SELECT * FROM test WHERE %where%');

    $sql->addCondition('c1=:c1 OR c2=:c2');

    $this->assertEqual($sql->toString(),
                       'SELECT * FROM test WHERE (c1=:c1 OR c2=:c2)');
  }

  function testAddConditionNoWhereClause()
  {
    $sql = new ComplexSelectSQL('SELECT * FROM test %where%');

    $sql->addCondition('c1=:c1 OR c2=:c2');

    $this->assertEqual($sql->toString(),
                       'SELECT * FROM test WHERE (c1=:c1 OR c2=:c2)');
  }

  function testAddSeveralConditions()
  {
    $sql = new ComplexSelectSQL('SELECT * FROM test %where%');

    $sql->addCondition('c1=:c1');
    $sql->addCondition('c2=:c2');

    $this->assertEqual($sql->toString(),
                       'SELECT * FROM test WHERE (c1=:c1) AND (c2=:c2)');
  }

  function testEmptyOrder()
  {
    $sql = new ComplexSelectSQL('SELECT * FROM test %order%');

    $this->assertEqual($sql->toString(),
                       'SELECT * FROM test');
  }

  function testAddOrderNoOrderClause()
  {
    $sql = new ComplexSelectSQL('SELECT * FROM test %order%');

    $sql->addOrder('t1');
    $sql->addOrder('t2', 'DESC');

    $this->assertEqual($sql->toString(),
                       'SELECT * FROM test ORDER BY t1 ASC,t2 DESC');
  }

  function testAddOrderWithOrderClause()
  {
    $sql = new ComplexSelectSQL('SELECT * FROM test ORDER BY %order%');

    $sql->addOrder('t1');
    $sql->addOrder('t2', 'DESC');

    $this->assertEqual($sql->toString(),
                       'SELECT * FROM test ORDER BY t1 ASC,t2 DESC');
  }

  function testAddOrderWithOrderClause2()
  {
    $sql = new ComplexSelectSQL('SELECT * FROM test ORDER BY t0 DESC %order%');

    $sql->addOrder('t1');
    $sql->addOrder('t2', 'DESC');

    $this->assertEqual($sql->toString(),
                       'SELECT * FROM test ORDER BY t0 DESC ,t1 ASC,t2 DESC');
  }

  function testAddOrderWithOrderClause3()
  {
    $sql = new ComplexSelectSQL('SELECT * FROM test ORDER BY t0 DESC %order%');

    $this->assertEqual($sql->toString(),
                       'SELECT * FROM test ORDER BY t0 DESC');
  }

  function testNoGroupsAdded()
  {
    $sql = new ComplexSelectSQL('SELECT * FROM test');

    $this->assertEqual($sql->toString(),
                       'SELECT * FROM test');
  }

  function testNoGroupsAdded2()
  {
    $sql = new ComplexSelectSQL('SELECT * FROM test GROUP BY t0 %group_by%');

    $this->assertEqual($sql->toString(),
                       'SELECT * FROM test GROUP BY t0');
  }

  function testAddGroupBy()
  {
    $sql = new ComplexSelectSQL('SELECT * FROM test %group_by%');

    $sql->addGroupBy('t1');
    $sql->addGroupBy('t2');

    $this->assertEqual($sql->toString(),
                       'SELECT * FROM test GROUP BY t1,t2');
  }

  function testAddGroupByWithGroupByClause()
  {
    $sql = new ComplexSelectSQL('SELECT * FROM test GROUP BY %group_by%');

    $sql->addGroupBy('t1');
    $sql->addGroupBy('t2');

    $this->assertEqual($sql->toString(),
                       'SELECT * FROM test GROUP BY t1,t2');
  }

  function testAddGroupByWithGroupByClause2()
  {
    $sql = new ComplexSelectSQL('SELECT * FROM test GROUP BY t0 %group_by%');

    $sql->addGroupBy('t1');
    $sql->addGroupBy('t2');

    $this->assertEqual($sql->toString(),
                       'SELECT * FROM test GROUP BY t0 ,t1,t2');
  }

}
?>
