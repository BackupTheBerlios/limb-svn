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
require_once(LIMB_DIR . '/core/finders/VersionedOneTableObjectsRawFinder.class.php');
require_once(LIMB_DIR . '/core/db/LimbDbTable.class.php');
require_once(LIMB_DIR . '/core/LimbToolkit.interface.php');

Mock :: generate('LimbToolkit');
Mock :: generate('LimbDbTable');

class TestVersionedOneTableObjectsRawFinder extends VersionedOneTableObjectsRawFinder
{
  function _defineDbTableName()
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

  function VersionedOneTableObjectsRawFinderTest()
  {
    parent :: LimbTestCase('versioned one table objects finder test');
  }

  function setUp()
  {
    $this->finder = new VersionedOneTableObjectsRawFinderTestVersion($this);
    $this->db_table = new MockLimbDbTable($this);
    $this->toolkit = new MockLimbToolkit($this);

    $this->toolkit->setReturnReference('createDBTable', $this->db_table);

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
}

?>