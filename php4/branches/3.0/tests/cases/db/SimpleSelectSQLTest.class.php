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
require_once(LIMB_DIR . '/core/db/SimpleSelectSQL.class.php');

class SimpleSelectSQLTest extends LimbTestCase
{
  var $sql;

  function SimpleSelectSQLTest()
  {
    parent :: LimbTestCase('simple select sql tests');
  }

  function setUp()
  {
    $this->sql = new SimpleSelectSQL('test');
  }

  function testSelectAllSQL()
  {
    $this->assertEqual($this->sql->toString(), 'SELECT test.* FROM test');
  }

  function testAddField()
  {
    $this->sql->addField('t1');
    $this->sql->addField('t2');

    $this->assertEqual($this->sql->toString(), 'SELECT test.t1,test.t2 FROM test');
  }

  function testAddJoinAllFields()
  {
    $this->sql->addJoin('article', array('article_id' => 'id'));

    $expected = 'SELECT test.*,' .
                'article.* ' .
                'FROM test ' .
                'LEFT JOIN article AS article ON test.article_id = article.id';

    $this->assertEqual($this->sql->toString(), $expected);
  }

  function testAddJoinWithFields()
  {
    $this->sql->addJoin('article', array('article_id' => 'id'), array('id', 'author'), 'art');

    $expected = 'SELECT test.*,' .
                'art.id,art.author ' .
                'FROM test ' .
                'LEFT JOIN article AS art ON test.article_id = art.id';

    $this->assertEqual($this->sql->toString(), $expected);
  }

  function testAddJoinMixedFieldsAliases()
  {
    $this->sql->addJoin('article', array('article_id' => 'id'), array('id', 'author' => 'auth'), 'art');

    $expected = 'SELECT test.*,' .
                'art.id,art.author AS auth ' .
                'FROM test ' .
                'LEFT JOIN article AS art ON test.article_id = art.id';

    $this->assertEqual($this->sql->toString(), $expected);
  }

  function testAddCondition()
  {
    $this->sql->addCondition('c1=:c1:');

    $this->assertEqual($this->sql->toString(),
                       'SELECT test.* FROM test WHERE (c1=:c1:)');
  }

  function testAddSeveralConditions()
  {
    $this->sql->addCondition('c1=:c1:');
    $this->sql->addCondition('c2=:c2:');

    $this->assertEqual($this->sql->toString(),
                       'SELECT test.* FROM test WHERE (c1=:c1:) AND (c2=:c2:)');
  }

  function testAddOrder()
  {
    $this->sql->addOrder('t1');
    $this->sql->addOrder('t2', 'DESC');

    $this->assertEqual($this->sql->toString(),
                       'SELECT test.* FROM test   ORDER BY t1 ASC,t2 DESC');
  }

  function testAddGroupBy()
  {
    $this->sql->addGroupBy('t1');
    $this->sql->addGroupBy('t2');

    $this->assertEqual($this->sql->toString(),
                       'SELECT test.* FROM test  GROUP BY t1,t2');
  }

  function testAddGroupBeforeOrderBy()
  {
    $this->sql->addOrder('t1');
    $this->sql->addGroupBy('t1');
    $this->sql->addGroupBy('t2');

    $this->assertEqual($this->sql->toString(),
                       'SELECT test.* FROM test  GROUP BY t1,t2 ORDER BY t1 ASC');
  }
}
?>
