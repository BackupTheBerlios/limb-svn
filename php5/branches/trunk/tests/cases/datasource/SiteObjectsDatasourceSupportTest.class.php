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
require_once(LIMB_DIR . '/class/core/datasources/site_objects_datasource_support.inc.php');
require_once(LIMB_DIR . '/class/core/site_objects/SiteObject.class.php');
require_once(LIMB_DIR . '/class/core/tree/Tree.interface.php');
require_once(LIMB_DIR . '/class/core/LimbToolkit.interface.php');

Mock :: generate('SiteObject');
Mock :: generate('Tree');
Mock :: generate('LimbToolkit');

class SiteObjectsDatasourceSupportTest extends LimbTestCase
{
  var $site_object;
  var $tree;
  var $toolkit;

  function setUp()
  {
    $this->site_object = new MockSiteObject($this);
    $this->tree = new MockTree($this);
    $this->toolkit = new MockLimbToolkit($this);

    Limb :: registerToolkit($this->toolkit);
  }

  function tearDown()
  {
    $this->site_object->tally();
    $this->tree->tally();
    $this->toolkit->tally();

    Limb :: popToolkit();
  }

  function testAssignPathsToSiteObjectsWorstCase()
  {
    $this->toolkit->expectOnce('getTree');
    $this->toolkit->setReturnValue('getTree', $this->tree);

    $objects_array = array(array('parent_node_id' => 10, 'node_id' => 100, 'identifier' => '1'),
                           array('parent_node_id' => 20, 'node_id' => 200, 'identifier' => '2'),
                           array('parent_node_id' => 30, 'node_id' => 300, 'identifier' => '3'));

    $this->tree->expectCallCount('getParents', 3);
    $this->tree->setReturnValueAt(0, 'getParents', array(array('identifier' => '-1')), array(100));
    $this->tree->setReturnValueAt(1, 'getParents', array(array('identifier' => '-2')), array(200));
    $this->tree->setReturnValueAt(2, 'getParents', array(array('identifier' => '-3')), array(300));

    assignPathsToSiteObjects($objects_array);

    $this->assertEqual($objects_array[0]['path'], '/-1/1');
    $this->assertEqual($objects_array[1]['path'], '/-2/2');
    $this->assertEqual($objects_array[2]['path'], '/-3/3');
  }

  function testAssignPathsToSiteObjectsParentsCache()
  {
    $this->toolkit->expectOnce('getTree');
    $this->toolkit->setReturnValue('getTree', $this->tree);

    $objects_array = array(array('parent_node_id' => 10, 'node_id' => 100, 'identifier' => '1'),
                           array('parent_node_id' => 10, 'node_id' => 200, 'identifier' => '2'),
                           array('parent_node_id' => 30, 'node_id' => 300, 'identifier' => '3'));

    $this->tree->expectCallCount('getParents', 2);
    $this->tree->setReturnValueAt(0, 'getParents', array(array('identifier' => '-1')), array(100));
    $this->tree->setReturnValueAt(1, 'getParents', array(array('identifier' => '-3')), array(300));

    assignPathsToSiteObjects($objects_array);

    $this->assertEqual($objects_array[0]['path'], '/-1/1');
    $this->assertEqual($objects_array[1]['path'], '/-1/2');
    $this->assertEqual($objects_array[2]['path'], '/-3/3');
  }

  function testAssignPathsToSiteObjectsAppend()
  {
    $this->toolkit->expectOnce('getTree');
    $this->toolkit->setReturnValue('getTree', $this->tree);

    $objects_array = array(array('parent_node_id' => 10, 'node_id' => 100, 'identifier' => '1'),
                           array('parent_node_id' => 10, 'node_id' => 300, 'identifier' => '3'));

    $this->tree->setReturnValue('getParents', array(array('identifier' => '-1')), array(100));

    assignPathsToSiteObjects($objects_array, '-append');

    $this->assertEqual($objects_array[0]['path'], '/-1/1-append');
    $this->assertEqual($objects_array[1]['path'], '/-1/3-append');
  }

  function testWrapWithSiteObjectEmptyArray()
  {
    $fetched_data = array();

    $this->toolkit->expectNever('createSiteObject');

    $this->assertTrue(wrapWithSiteObject($fetched_data) === false);
  }

  function testWrapWithSiteObjectSingle()
  {
    $fetched_data = array('class_name' => $class_name = 'test_site_object');

    $this->toolkit->setReturnValue('createSiteObject', $this->site_object, array($class_name));
    $this->toolkit->expectOnce('createSiteObject', array($class_name));

    $this->site_object->expectOnce('merge', array($fetched_data));

    $this->assertTrue(wrapWithSiteObject($fetched_data) === $this->site_object);
  }

  function testWrapWithSiteObjectMultiple()
  {
    $fetched_data = array($data1 = array('class_name' => $class_name1 = 'test_site_object1'),
                          $data2 = array('class_name' => $class_name2 = 'test_site_object2'));

    $this->toolkit->setReturnValue('createSiteObject', $this->site_object, array($class_name1));
    $this->toolkit->setReturnValue('createSiteObject', $this->site_object, array($class_name2));
    $this->toolkit->expectCallCount('createSiteObject', 2);

    $this->site_object->expectArgumentsAt(0, 'merge', array($data1));
    $this->site_object->expectArgumentsAt(1, 'merge', array($data2));

    $this->assertTrue(wrapWithSiteObject($fetched_data) === array($this->site_object, $this->site_object));

  }

}

?>