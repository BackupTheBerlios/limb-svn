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
require_once(LIMB_DIR . '/core/db/SimpleQueryBuilder.class.php');

class SimpleQueryBuilderTest extends LimbTestCase
{
  function SimpleQueryBuilderTest()
  {
    parent :: LimbTestCase('simple query builder tests');
  }

  function testBuildSelectAllSQL()
  {
    $sql = SimpleQueryBuilder :: buildSelectSQL('test');

    $this->assertEqual($sql, 'SELECT * FROM test');
  }

  function testBuildSelectSpecificColumnsSQL()
  {
    $sql = SimpleQueryBuilder :: buildSelectSQL('test', 't1, t2');

    $this->assertEqual($sql, 'SELECT t1, t2 FROM test');
  }

  function testBuildSelectSpecificColumnsSQL2()
  {
    $sql = SimpleQueryBuilder :: buildSelectSQL('test', array('t1', 't2'));

    $this->assertEqual($sql, 'SELECT t1, t2 FROM test');
  }

  function testBuildSelectArrayFilteredColumnsSQL()
  {
    $sql = SimpleQueryBuilder :: buildSelectSQL('test', '*', array('c1', 'c2'));

    $this->assertEqual($sql, 'SELECT * FROM test WHERE ((c1=:c1) AND (c2=:c2))');
  }

  function testBuildSelectMixedArrayFilteredColumnsSQL()
  {
    $sql = SimpleQueryBuilder :: buildSelectSQL('test', '*', array('c1' => 'whatever', 'c2'));

    $this->assertEqual($sql, 'SELECT * FROM test WHERE ((c1=:c1) AND (c2=:c2))');
  }

  function testBuildSelectRawFilteredColumnsSQL()
  {
    $sql = SimpleQueryBuilder :: buildSelectSQL('test', '*', '(c1 > c2)');

    $this->assertEqual($sql, 'SELECT * FROM test WHERE (c1 > c2)');
  }

  function testBuildSelectOrderedSQL()
  {
    $sql = SimpleQueryBuilder :: buildSelectSQL('test', '*', null, 'ASC t1, DESC t2');

    $this->assertEqual($sql, 'SELECT * FROM test  ORDER BY ASC t1, DESC t2');
  }

  function testBuildInsertSQL()
  {
    $sql = SimpleQueryBuilder :: buildInsertSQL('test', array('id', 'name', 'title'));

    $this->assertEqual($sql, 'INSERT INTO test (id, name, title) VALUES (:id, :name, :title)');
  }

  function testBuildUpdateAllSQL()
  {
    $sql = SimpleQueryBuilder :: buildUpdateSQL('test', array('id', 'name', 'title'));
    $prefix = SimpleQueryBuilder :: getUpdatePrefix();

    $this->assertEqual($sql, "UPDATE test SET id=:{$prefix}id, name=:{$prefix}name, title=:{$prefix}title");
  }

  function testBuildUpdateArrayFilteredSQL()
  {
    $sql = SimpleQueryBuilder :: buildUpdateSQL('test', array('id', 'name', 'title'), array('c1', 'c2'));
    $prefix = SimpleQueryBuilder :: getUpdatePrefix();

    $this->assertEqual($sql, "UPDATE test SET id=:{$prefix}id, name=:{$prefix}name, title=:{$prefix}title WHERE ((c1=:c1) AND (c2=:c2))");
  }

  function testBuildUpdateMixedArrayFilteredSQL()
  {
    $sql = SimpleQueryBuilder :: buildUpdateSQL('test', array('id', 'name', 'title'), array('c1', 'c2' => 'value'));
    $prefix = SimpleQueryBuilder :: getUpdatePrefix();

    $this->assertEqual($sql, "UPDATE test SET id=:{$prefix}id, name=:{$prefix}name, title=:{$prefix}title WHERE ((c1=:c1) AND (c2=:c2))");
  }

  function testBuildUpdateRawFilteredSQL()
  {
    $sql = SimpleQueryBuilder :: buildUpdateSQL('test', array('id', 'name', 'title'), 'c1 = c2');
    $prefix = SimpleQueryBuilder :: getUpdatePrefix();

    $this->assertEqual($sql, "UPDATE test SET id=:{$prefix}id, name=:{$prefix}name, title=:{$prefix}title WHERE c1 = c2");
  }

  function testBuildDeleteAllSQL()
  {
    $sql = SimpleQueryBuilder :: buildDeleteSQL('test');

    $this->assertEqual($sql, 'DELETE FROM test');
  }

  function testBuildDeleteArrayFilteredSQL()
  {
    $sql = SimpleQueryBuilder :: buildDeleteSQL('test', array('c1', 'c2'));

    $this->assertEqual($sql, 'DELETE FROM test WHERE ((c1=:c1) AND (c2=:c2))');
  }

  function testBuildDeleteMixedArrayFilteredSQL()
  {
    $sql = SimpleQueryBuilder :: buildDeleteSQL('test', array('c1' => 'value', 'c2'));

    $this->assertEqual($sql, 'DELETE FROM test WHERE ((c1=:c1) AND (c2=:c2))');
  }

  function testBuildDeleteRawFilteredSQL()
  {
    $sql = SimpleQueryBuilder :: buildDeleteSQL('test', 'c1 = c2');

    $this->assertEqual($sql, 'DELETE FROM test WHERE c1 = c2');
  }

  function testAndCondition()
  {
    $this->assertEqual('(c=:c)', SimpleQueryBuilder :: andCondition(array('c')));
  }

  function testAndCondition2()
  {
    $this->assertEqual('(c1=:c1) AND (c2=:c2)', SimpleQueryBuilder :: andCondition(array('c1', 'c2')));
  }

  function testInCondition()
  {
    $this->assertEqual('t IN (:c1, :c2)', SimpleQueryBuilder :: inCondition('t', array('c1', 'c2')));
  }
}
?>
