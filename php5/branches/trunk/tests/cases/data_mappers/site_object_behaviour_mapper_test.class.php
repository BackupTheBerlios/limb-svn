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
require_once(LIMB_DIR . '/class/lib/db/db_factory.class.php');
require_once(LIMB_DIR . '/class/core/data_mappers/site_object_behaviour_mapper.class.php');
require_once(LIMB_DIR . '/class/core/behaviours/site_object_behaviour.class.php');

Mock :: generatePartial('site_object_behaviour_mapper',
                        'site_object_behaviour_mapper_test_version',
                        array('insert', 'update'));

class site_object_behaviour_mapper_test extends LimbTestCase
{
  var $db;
  var $mapper;

  function setUp()
  {
    $this->mapper = new site_object_behaviour_mapper();
    $this->db = db_factory :: instance();

    $this->_clean_up();
  }

  function tearDown()
  {
    $this->_clean_up();
  }

  function _clean_up()
  {
    $this->db->sql_delete('sys_behaviour');
  }

  function test_find_by_id_null()
  {
    $this->assertNull($this->mapper->find_by_id(1000));
  }

  function test_find_by_id()
  {
    $this->db->sql_insert('sys_behaviour', array('id' => $id = 100, 'name' => 'site_object_behaviour'));

    $behaviour = $this->mapper->find_by_id($id);

    $this->assertIsA($behaviour, 'site_object_behaviour');
    $this->assertEqual($id, $behaviour->get_id());
  }

  function test_save_insert()
  {
    $mapper = new site_object_behaviour_mapper_test_version($this);

    $behaviour = new site_object_behaviour();

    $mapper->expectOnce('insert', array($behaviour));

    $mapper->save($behaviour);

    $mapper->tally();
  }

  function test_save_update()
  {
    $mapper = new site_object_behaviour_mapper_test_version($this);

    $behaviour = new site_object_behaviour();
    $behaviour->set_id(100);

    $mapper->expectOnce('update', array($behaviour));

    $mapper->save($behaviour);

    $mapper->tally();
  }

  function test_insert()
  {
    $behaviour = new site_object_behaviour();

    $this->mapper->insert($behaviour);

    $this->db->sql_select('sys_behaviour', '*', 'id=' . $behaviour->get_id());

    $record = $this->db->fetch_row();

    $this->assertEqual($record['name'], get_class($behaviour));
  }

  function test_update_failed_no_id()
  {
    $behaviour = new site_object_behaviour();

    try
    {
      $this->mapper->update($behaviour);
      $this->assertTrue(false);
    }
    catch(LimbException $e){}
  }

  function test_update()
  {
    $this->db->sql_insert('sys_behaviour', array('id' => $id = 100));

    $behaviour = new site_object_behaviour();
    $behaviour->set_id($id);

    $this->mapper->update($behaviour);

    $this->db->sql_select('sys_behaviour', '*', 'id=' . $behaviour->get_id());

    $record = $this->db->fetch_row();

    $this->assertEqual($record['name'], get_class($behaviour));
  }

  function test_delete_failed_no_id()
  {
    $behaviour = new site_object_behaviour();

    try
    {
      $this->mapper->delete($behaviour);
      $this->assertTrue(false);
    }
    catch(LimbException $e){}
  }

  function test_delete()
  {
    $this->db->sql_insert('sys_behaviour', array('id' => $id = 100));

    $behaviour = new site_object_behaviour();
    $behaviour->set_id($id);

    $this->mapper->delete($behaviour);

    $this->db->sql_select('sys_behaviour', '*', 'id=' . $behaviour->get_id());

    $this->assertTrue(!$this->db->fetch_row());
  }
}

?>