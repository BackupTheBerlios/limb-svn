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
    $sql = new ComplexSelectSQL("SELECT t3 \n%fields%,t4 FROM test");

    $sql->addField('t1');
    $sql->addField('t2');

    $this->assertEqual($sql->toString(), "SELECT t3 \n,t1,t2,t4 FROM test");
  }

  function testNoFieldsAdded()
  {
    $sql = new ComplexSelectSQL("SELECT t3 \n%fields%,t4 FROM test");

    $this->assertEqual($sql->toString(), "SELECT t3 \n,t4 FROM test");
  }

  function testAddFieldNoFields()
  {
    $sql = new ComplexSelectSQL('SELECT %fields% FROM test');

    $sql->addField('t1');
    $sql->addField('t2');

    $this->assertEqual($sql->toString(), 'SELECT t1,t2 FROM test');
  }

  function testNoAddTable()
  {
    $sql = new ComplexSelectSQL('SELECT * FROM test %tables%');

    $this->assertEqual($sql->toString(), 'SELECT * FROM test');
  }

  function testAddTable()
  {
    $sql = new ComplexSelectSQL("SELECT * FROM test \n\t%tables%");

    $sql->addTable('test2 AS t2');
    $sql->addTable('test3');

    $this->assertEqual($sql->toString(), "SELECT * FROM test \n\t,test2 AS t2,test3");
  }

  function testAddLeftJoin()
  {
    $sql = new ComplexSelectSQL('SELECT * FROM test %left_join%');

    $sql->addLeftJoin('article', array('test.article_id' => 'article.id'));

    $this->assertEqual($sql->toString(),
                       'SELECT * FROM test LEFT JOIN article ON test.article_id=article.id');
  }

  function testEmptyCondition()
  {
    $sql = new ComplexSelectSQL('SELECT * FROM test %where%');

    $this->assertEqual($sql->toString(),
                       'SELECT * FROM test');
  }

  function testAddCondition()
  {
    $sql = new ComplexSelectSQL("SELECT * FROM test WHERE \n%where%");

    $sql->addCondition('c1=:c1 OR c2=:c2');

    $this->assertEqual($sql->toString(),
                       "SELECT * FROM test WHERE \n(c1=:c1 OR c2=:c2)");
  }

  function testAddConditionNoWhereClause()
  {
    $sql = new ComplexSelectSQL("SELECT * FROM test \n%where%");

    $sql->addCondition('c1=:c1 OR c2=:c2');

    $this->assertEqual($sql->toString(),
                       "SELECT * FROM test \nWHERE (c1=:c1 OR c2=:c2)");
  }

  function testAddSeveralConditions()
  {
    $sql = new ComplexSelectSQL('SELECT * FROM test %where%');

    $sql->addCondition('c1=:c1');
    $sql->addCondition('c2=:c2');

    $this->assertEqual($sql->toString(),
                       'SELECT * FROM test WHERE (c1=:c1) AND (c2=:c2)');
  }

  function testAddConditionToExistingConditions()
  {
    $sql = new ComplexSelectSQL("SELECT * FROM test WHERE t1=t1\n %where%");

    $sql->addCondition('c1=:c1');
    $sql->addCondition('c2=:c2');

    $this->assertEqual($sql->toString(),
                       "SELECT * FROM test WHERE t1=t1\n AND (c1=:c1) AND (c2=:c2)");
  }

  function testAddConditionToExistingConditionsWithOrder()
  {
    $sql = new ComplexSelectSQL("SELECT * FROM test WHERE t1=t1\n\n %where% \n\tORDER BY t1");

    $sql->addCondition('c1=:c1');
    $sql->addCondition('c2=:c2');

    $this->assertEqual($sql->toString(),
                       "SELECT * FROM test WHERE t1=t1\n\n AND (c1=:c1) AND (c2=:c2) \n\tORDER BY t1");
  }

  function testAddConditionToExistingConditionsWithGroup()
  {
    $sql = new ComplexSelectSQL("SELECT * FROM test WHERE t1=t1\n\n %where% \n\tGROUP BY t1");

    $sql->addCondition('c1=:c1');
    $sql->addCondition('c2=:c2');

    $this->assertEqual($sql->toString(),
                       "SELECT * FROM test WHERE t1=t1\n\n AND (c1=:c1) AND (c2=:c2) \n\tGROUP BY t1");
  }

  function testEmptyOrder()
  {
    $sql = new ComplexSelectSQL("SELECT * FROM test \n%order%");

    $this->assertEqual($sql->toString(),
                       'SELECT * FROM test');
  }

  function testAddOrderNoOrderClause()
  {
    $sql = new ComplexSelectSQL("SELECT * FROM test \n%order%");

    $sql->addOrder('t1');
    $sql->addOrder('t2', 'DESC');

    $this->assertEqual($sql->toString(),
                       "SELECT * FROM test \nORDER BY t1 ASC,t2 DESC");
  }

  function testAddOrderWithOrderClause()
  {
    $sql = new ComplexSelectSQL("SELECT * FROM test ORDER BY\n %order%");

    $sql->addOrder('t1');
    $sql->addOrder('t2', 'DESC');

    $this->assertEqual($sql->toString(),
                       "SELECT * FROM test ORDER BY\n t1 ASC,t2 DESC");
  }

  function testAddOrderWithOrderClause2()
  {
    $sql = new ComplexSelectSQL("SELECT * FROM test ORDER BY t0 DESC\n %order%");

    $sql->addOrder('t1');
    $sql->addOrder('t2', 'DESC');

    $this->assertEqual($sql->toString(),
                       "SELECT * FROM test ORDER BY t0 DESC\n ,t1 ASC,t2 DESC");
  }

  function testAddOrderWithOrderClause3()
  {
    $sql = new ComplexSelectSQL("SELECT * FROM test ORDER BY t0 DESC\n %order%");

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
    $sql = new ComplexSelectSQL("SELECT * FROM test GROUP BY t0 \n%group%");

    $this->assertEqual($sql->toString(),
                       "SELECT * FROM test GROUP BY t0");
  }

  function testAddGroupBy()
  {
    $sql = new ComplexSelectSQL('SELECT * FROM test %group%');

    $sql->addGroupBy('t1');
    $sql->addGroupBy('t2');

    $this->assertEqual($sql->toString(),
                       'SELECT * FROM test GROUP BY t1,t2');
  }

  function testAddGroupByWithGroupByClause()
  {
    $sql = new ComplexSelectSQL("SELECT * FROM test GROUP BY \n%group%");

    $sql->addGroupBy('t1');
    $sql->addGroupBy('t2');

    $this->assertEqual($sql->toString(),
                       "SELECT * FROM test GROUP BY \nt1,t2");
  }

  function testAddGroupByWithGroupByClause2()
  {
    $sql = new ComplexSelectSQL("SELECT * FROM test GROUP BY t0 \n%group%");

    $sql->addGroupBy('t1');
    $sql->addGroupBy('t2');

    $this->assertEqual($sql->toString(),
                       "SELECT * FROM test GROUP BY t0 \n,t1,t2");
  }

}
?>
