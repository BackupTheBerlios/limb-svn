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
require_once(LIMB_DIR . '/class/core/finders/VersionedOneTableObjectsRawFinder.class.php');
require_once(LIMB_DIR . '/class/lib/db/DbTable.class.php');

Mock :: generate('LimbToolkit');
Mock :: generate('DbTable');

class TestVersionedOneTableObjectsRawFinder extends VersionedOneTableObjectsRawFinder
{
  protected function _defineDbTableName()
  {
    return 'table1';
  }
}

Mock :: generatePartial('TestVersionedOneTableObjectsRawFinder',
                        'VersionedOneTableObjectsRawFinderTestVersion',
                        array('_doParentFind',
                              '_doParentFindCount'));

class VersionedOneTableObjectsRawFinderTest extends LimbTestCase
{
  var $finder;
  var $toolkit;
  var $db_table;

  function setUp()
  {
    $this->finder = new VersionedOneTableObjectsRawFinderTestVersion($this);
    $this->db_table = new MockDbTable($this);
    $this->toolkit = new MockLimbToolkit($this);

    $this->toolkit->setReturnValue('createDBTable', $this->db_table);

    Limb :: registerToolkit($this->toolkit);
  }

  function tearDown()
  {
    $this->toolkit->tally();
    $this->db_table->tally();
    $this->finder->tally();

    Limb :: popToolkit();
  }

  function testFind()
  {
    $params['limit'] = 5;
    $sql_params['conditions'][] = 'some condition';

    $expected_sql_params = $sql_params;
    $expected_sql_params['conditions'][] = ' AND sso.current_version=tn.version';

    $this->finder->expectOnce('_doParentFind', array(new EqualExpectation($params),
                                                        new EqualExpectation($expected_sql_params)));
    $this->finder->setReturnValue('_doParentFind', $result = 'some result');

    $this->assertEqual($this->finder->find($params, $sql_params), $result);
  }

  function testFindByVersion()
  {
    $expected_sql_params = array();
    $expected_sql_params['conditions'][] = ' AND sso.id=' . $object_id = 100;
    $expected_sql_params['conditions'][] = ' AND tn.version=' . $version = 1000;

    $this->finder->expectOnce('_doParentFind', array(array(),
                                                        new EqualExpectation($expected_sql_params)));
    $this->finder->setReturnValue('_doParentFind', $result = 'some result');

    $this->assertEqual($this->finder->findByVersion($object_id, $version), $result);
  }

  function testFindCount()
  {
    $sql_params['conditions'][] = 'some condition';
    $expected_sql_params = $sql_params;

    $expected_sql_params['conditions'][] = ' AND sso.current_version=tn.version';

    $this->finder->expectOnce('_doParentFindCount',
                              array(new EqualExpectation($expected_sql_params)));

    $this->finder->setReturnValue('_doParentFindCount', $result = 'some result');

    $this->assertEqual($this->finder->findCount($sql_params), $result);
  }

}

?>