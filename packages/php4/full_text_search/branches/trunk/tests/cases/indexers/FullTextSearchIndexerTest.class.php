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
require_once(LIMB_DIR . '/class/lib/db/DbFactory.class.php');
require_once(LIMB_DIR . '/class/site_objects/SiteObject.class.php');
require_once(dirname(__FILE__) . '/../../../indexers/FullTextIndexer.class.php');
require_once(LIMB_DIR . '/class/behaviours/SiteObjectBehaviour.class.php');

Mock :: generate('SiteObject');
Mock :: generate('SiteObjectBehaviour');

class FullTextSearchIndexerTest extends LimbTestCase
{
  var $db = null;
  var $site_object = null;
  var $indexer = null;

  function setUp()
  {
    $this->db =& DbFactory :: instance();

    $this->_cleanUp();

    $this->indexer = new FullTextIndexer();

    $this->site_object = new MockSiteObject($this);

    $this->site_object->setReturnValue('getId', 10);
    $this->site_object->setReturnValue('getClassId', 5);
    $this->site_object->setReturnValue('export',
      array(
        'id' => 10,
        'title' => ,
        'content' => ,
        'noSearch' => 'wow',
        'defaultWeightField' => 'this is a field'
      )
    );

    $this->behaviour = new MockSiteObjectBehaviour($this);

    $attributes_definition = array(
        'title' => array('search' => true, 'search_weight' => 10),
        'content' => array('search' => true, 'search_weight' => 5),
        'no_search' => array(),
        'default_weight_field' => array('search' => true),
    );

    foreach($attributes_definition as $id => $definition)
      $this->behaviour->setReturnValue('getDefinition', $definition, array($id));

    $this->site_object->setReturnReference('getBehaviour', $this->behaviour);
  }

  function tearDown()
  {
    $this->_cleanUp();
    $this->site_object->tally();
  }

  function _cleanUp()
  {
    $this->db->sqlDelete('sys_full_text_index');
  }

  function testIndexObjectNoWordsInDb()
  {
    $this->site_object->expectAtLeastOnce('getId');
    $this->site_object->expectAtLeastOnce('getClassId');
    $this->site_object->expectAtLeastOnce('export');

    $this->indexer->add($this->site_object);

    $this->db->sqlSelect('sys_full_text_index', '*', '', 'id');
    $arr = $this->db->getArray();

    $this->assertNotEqual($arr, array());
    $this->assertEqual(sizeof($arr), 3);

    $record = reset($arr);
    $this->assertEqual($record['attribute'], 'title');
    $this->assertEqual((int)$record['object_id'], $this->site_object->getId());
    $this->assertEqual((int)$record['class_id'], $this->site_object->getClassId());
    $this->assertEqual($record['body'], 'this is a test title');
    $this->assertEqual($record['weight'], 10);

    $record = next($arr);
    $this->assertEqual($record['attribute'], 'content');
    $this->assertEqual((int)$record['object_id'], $this->site_object->getId());
    $this->assertEqual((int)$record['class_id'], $this->site_object->getClassId());
    $this->assertEqual($record['body'], 'this is a content test');
    $this->assertEqual($record['weight'], 5);

    $record = next($arr);
    $this->assertEqual($record['attribute'], 'default_weight_field');
    $this->assertEqual((int)$record['object_id'], $this->site_object->getId());
    $this->assertEqual((int)$record['class_id'], $this->site_object->getClassId());
    $this->assertEqual($record['body'], 'this is a field');
    $this->assertEqual($record['weight'], 1);
  }

  function testIndex2_equalObjects()
  {
    $this->indexer->add($this->site_object);
    $this->indexer->add($this->site_object);

    $this->testIndexObjectNoWordsInDb();
  }

}
?>