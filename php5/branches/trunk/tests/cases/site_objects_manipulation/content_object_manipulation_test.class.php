<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/ 
require_once(dirname(__FILE__) . '/site_object_manipulation_test.class.php');
require_once(LIMB_DIR . '/class/lib/db/db_factory.class.php');
require_once(LIMB_DIR . '/class/core/site_objects/content_object.class.php');
require_once(LIMB_DIR . '/class/db_tables/content_object_db_table.class.php');
require_once(LIMB_DIR . '/class/core/base_limb_toolkit.class.php');
require_once(LIMB_DIR . '/class/core/permissions/user.class.php');

Mock :: generatePartial('BaseLimbToolkit',
                      'ContentObjectToolkitMock', array());

Mock :: generate('User');

class ContentObjectManipulationTestToolkit extends ContentObjectToolkitMock
{
  var $_mocked_methods = array('getUser');
    
  public function getUser() 
  { 
    $args = func_get_args();
    return $this->_mock->_invoke('getUser', $args); 
  }
}

Mock :: generatePartial('content_object',
                        'content_object_manipulation_test_version',
                        array('_do_parent_create', '_do_parent_update', '_do_parent_delete'));

class content_object_manipulation_test_version_db_table extends content_object_db_table
{   
  function _define_db_table_name()
  {
    return 'test_content_object';
  }
  
  function _define_columns()
  {
    return complex_array :: array_merge(
      parent :: _define_columns(),
      array(
        'annotation' => '',
        'content' => '',
        'news_date' => array('type' => 'date'),
      )
    );
  }
}

class content_object_manipulation_test extends LimbTestCase 
{       
  var $db;
  var $object;
  
  var $toolkit;
  var $user;
  
  function setUp()
  {
    $this->user = new Mockuser($this);
    $this->user->setReturnValue('get_id', 25);

    $this->toolkit = new ContentObjectManipulationTestToolkit($this);
    $this->toolkit->setReturnValue('getUser', $this->user);
    
    Limb :: registerToolkit($this->toolkit);
    
    $this->db = db_factory :: instance();
    
    $this->_clean_up();
    
    $this->object = new content_object_manipulation_test_version($this);
    $this->object->__construct();   
  }
  
  function tearDown()
  {
    $this->toolkit->tally();
    $this->object->tally();
    
    $this->_clean_up();
    
    Limb :: popToolkit();
  }
    
  function _clean_up()
  {
    $this->db->sql_delete('sys_object_version');
    $this->db->sql_delete('test_content_object');
  }
  
  function test_get_db_table()
  {
    $this->assertIsA($this->object->get_db_table(), 'content_object_manipulation_test_version_db_table');
  }
  
  function test_create()
  {
    //we do it because we mock parent create call
    $this->object->set_id($new_object_id = 100);
    $this->object->set_version(1);
    
    $this->object->expectOnce('_do_parent_create', array(false));
    $this->object->setReturnValue('_do_parent_create', $new_object_id);
    
    $this->object->set('identifier', 'test');
    $this->object->set('title', 'Title');
    $this->object->set('annotation', 'news annotation');
    $this->object->set('content', 'news content');
    $this->object->set('news_date', '2004-01-02 00:00:00');
    
    $this->assertEqual($new_object_id, $this->object->create());
        
    $this->_check_content_object_record();
    $this->_check_sys_object_version_record();
  }
  
  function test_versioned_update()
  {
    $this->object->set_id($object_id = 100);
    
    //we do it because we mock parent update call
    $this->object->set_version(2);
    
    $this->db->sql_insert('test_content_object', array(
                                                   'object_id' => $object_id,
                                                   'identifier' => 'test',
                                                   'title' => 'Title',                                                   
                                                   'annotation' => 'news annotation',
                                                   'content' => 'news content',
                                                   'news_date' => '2000-01-02 00:00:00',
                                                   'version' => 1));
    
    $this->object->expectOnce('_do_parent_update', array(true));

    $this->object->set('identifier', 'test2');
    $this->object->set('title', 'Title2');    
    $this->object->set('annotation', 'news annotation2');
    $this->object->set('content', 'news content2');
    $this->object->set('news_date', '2004-01-02 00:00:00');
    
    $this->object->update();
    
    $this->db->sql_select('test_content_object');    
    $this->assertEqual(sizeof($this->db->get_array()), 2);
        
    $this->_check_content_object_record();
    $this->_check_sys_object_version_record();    
  }
  
  function test_unversioned_update_ok()
  {
    $this->object->set_id($object_id = 100);
    
    //we do it because we mock parent update call
    $this->object->set_version(1);
    
    $this->db->sql_insert('test_content_object', array(
                                                   'object_id' => $object_id,
                                                   'identifier' => 'test',
                                                   'title' => 'Title',                                                   
                                                   'annotation' => 'news annotation',
                                                   'content' => 'news content',
                                                   'news_date' => '2000-01-02 00:00:00',
                                                   'version' => 1));
    
    $this->object->expectOnce('_do_parent_update', array(false));

    $this->object->set('identifier', 'test2');
    $this->object->set('title', 'Title2');    
    $this->object->set('annotation', 'news annotation2');
    $this->object->set('content', 'news content2');
    $this->object->set('news_date', '2004-01-02 00:00:00');
    
    $this->object->update(false);

    $this->db->sql_select('test_content_object');    
    $this->assertEqual(sizeof($this->db->get_array()), 1);

    $this->_check_content_object_record();
  }  

  function test_unversioned_update_failed_no_previous_version_record()
  {
    $this->object->set_id($object_id = 100);
    //we do it because we mock parent update call
    $this->object->set_version(1);
    
    try
    {
      $this->object->update(false);
    }
    catch(LimbException $e)
    {
      $this->assertEqual($e->getMessage(), 'content record not found');
    }
  }
  
  function test_fetch_version_failed()
  {   
    $this->assertIdentical(false, $this->object->fetch_version(10000));
  }

  function test_delete()
  {
    $this->object->set_id($object_id = 100);

    $this->db->sql_insert('test_content_object', array(
                                                   'object_id' => $object_id,
                                                   'identifier' => 'test',
                                                   'title' => 'Title',                                                   
                                                   'annotation' => 'news annotation',
                                                   'content' => 'news content',
                                                   'news_date' => '2000-01-02 00:00:00',
                                                   'version' => 1));

    $this->db->sql_insert('test_content_object', array(
                                                   'object_id' => $object_id,
                                                   'identifier' => 'test2',
                                                   'title' => 'Title2',                                                   
                                                   'annotation' => 'news annotation',
                                                   'content' => 'news content',
                                                   'news_date' => '2000-01-02 00:00:00',
                                                   'version' => 2));
    
    $this->object->expectOnce('_do_parent_delete');
    $this->object->delete();
    
    $this->db->sql_select('test_content_object');    
    $this->assertEqual(sizeof($this->db->get_array()), 0);
  }
  
  function test_fetch_version()
  {
    $this->db->sql_insert('test_content_object', array(
                                                   'object_id' => $object_id = 1000,
                                                   'identifier' => 'test',
                                                   'title' => 'Title',                                                   
                                                   'annotation' => 'news annotation',
                                                   'content' => 'news content',
                                                   'news_date' => '2003-01-02 00:00:00',
                                                   'version' => 1));

    $this->db->sql_insert('test_content_object', array(
                                                   'object_id' => $object_id,
                                                   'identifier' => 'test2',
                                                   'title' => 'Title2',                                                   
                                                   'annotation' => 'news annotation2',
                                                   'content' => 'news content2',
                                                   'news_date' => '2000-01-02 00:00:00',
                                                   'version' => 2));
    
    $this->object->set_id($object_id);
    $version_data = $this->object->fetch_version(2);
    
    $this->assertFalse(isset($version_data['id']));
    
    $this->assertEqual($version_data['object_id'], $object_id);
    $this->assertEqual($version_data['identifier'], 'test2');
    $this->assertEqual($version_data['title'], 'Title2');    
    $this->assertEqual($version_data['annotation'], 'news annotation2');
    $this->assertEqual($version_data['content'], 'news content2');
    $this->assertEqual($version_data['news_date'], '2000-01-02 00:00:00');    
    
  }
  
  function test_failed_recover_version()
  {
    try
    {
      $this->object->recover_version(1);
      $this->assertTrue(false);
    }
    catch(LimbException $e)
    {
      $this->assertEqual($e->getMessage(), 'version record not found');
    }
  }
  
  function test_recover_version()
  {
    Mock :: generatePartial('content_object',
                          'content_object_test_recover_version',
                          array('update', 'merge', '_define_db_table_name'));
    
    $object = new content_object_test_recover_version($this);
    $object->__construct();
    
    $this->db->sql_insert('test_content_object', $data = array(
                                                   'object_id' => $object_id = 1000,
                                                   'identifier' => 'test',
                                                   'title' => 'Title',                                                   
                                                   'annotation' => 'news annotation',
                                                   'content' => 'news content',
                                                   'news_date' => '2003-01-02 00:00:00',
                                                   'version' => 2));
    
    $object->set_id($object_id);
    
    $version_data = $data;
    unset($version_data['version']);
        
    $object->setReturnValue('_define_db_table_name', 'content_object_manipulation_test_version');
    $object->expectOnce('merge', array(new EqualExpectation($version_data)));
    $object->expectOnce('update');
     
    $object->recover_version(2);
    
    $object->tally();
  }
  
  function test_trim_versions()
  {
    $this->db->sql_insert('test_content_object', array(
                                                   'object_id' => $object_id = 1000,
                                                   'identifier' => 'test',
                                                   'title' => 'Title',                                                   
                                                   'annotation' => 'news annotation',
                                                   'content' => 'news content',
                                                   'news_date' => '2003-01-02 00:00:00',
                                                   'version' => 1));
    
    $this->db->sql_insert('test_content_object', $data = array(
                                                   'object_id' => $object_id,
                                                   'identifier' => 'test',
                                                   'title' => 'Title',                                                   
                                                   'annotation' => 'news annotation',
                                                   'content' => 'news content',
                                                   'news_date' => '2003-01-02 00:00:00',
                                                   'version' => 2));
    
    $this->object->set_version(2);
    $this->object->set_id($object_id);
    $this->object->trim_versions();
    
    $this->db->sql_select('test_content_object');
    $arr = $this->db->get_array();        
    $this->assertEqual(sizeof($arr), 1);
    
    $record = current($arr);
    $this->assertEqual($record['version'], 2);
  }
    
  function _check_sys_object_version_record()
  {
    $conditions['object_id'] = $this->object->get_id();
    $conditions['version'] = $this->object->get_version();
  
    $this->db->sql_select('sys_object_version', '*', $conditions);
    $record = $this->db->fetch_row();
    
    $this->assertEqual($record['object_id'], $this->object->get_id());
    $this->assertEqual($record['version'], $this->object->get_version());
    $this->assertEqual($record['creator_id'], $this->user->get_id());
  } 

  function _check_content_object_record()
  {
    $conditions['object_id'] = $this->object->get_id();
    $conditions['version'] = $this->object->get_version();

    $db_table = $this->object->get_db_table();
    $arr = $db_table->get_list($conditions, 'id');
 
    $this->assertEqual(sizeof($arr), 1);
    $record = current($arr);
    
    $this->assertEqual($record['identifier'], $this->object->get_identifier());
    $this->assertEqual($record['title'], $this->object->get_title());
    $this->assertEqual($record['annotation'], $this->object->get('annotation'));
    $this->assertEqual($record['content'], $this->object->get('content'));
    $this->assertEqual($record['news_date'], $this->object->get('news_date'));
    
  }   
}

?>