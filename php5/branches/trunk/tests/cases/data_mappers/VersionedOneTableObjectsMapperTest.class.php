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
require_once(LIMB_DIR . '/class/core/data_mappers/versioned_one_table_objects_mapper.class.php');
require_once(LIMB_DIR . '/class/core/site_objects/versioned_site_object.class.php');
require_once(dirname(__FILE__) . '/one_table_objects_mapper_test_version_db_table.class.php');
require_once(LIMB_DIR . '/class/core/finders/versioned_one_table_objects_raw_finder.class.php');
require_once(LIMB_DIR . '/class/core/permissions/user.class.php');
require_once(LIMB_DIR . '/class/core/base_limb_toolkit.class.php');

Mock :: generatePartial('BaseLimbToolkit',
                      'VersionedOneTableObjectsToolkitMock', array());

class VersionedOneTableObjectsTestToolkitMock extends VersionedOneTableObjectsToolkitMock
{
  var $_mocked_methods = array('getUser');

  public function getUser()
  {
    $args = func_get_args();
    return $this->_mock->_invoke('getUser', $args);
  }
}

class versioned_one_table_objects_mapper_test_version extends versioned_one_table_objects_mapper
{
  protected function _create_domain_object()
  {
    return new versioned_one_table_objects_mapper_test_news_object();
  }

  protected function _define_db_table_name()
  {
    return 'one_table_objects_mapper_test_version';
  }
}

Mock :: generatePartial('versioned_one_table_objects_mapper_test_version',
                        'versioned_one_table_objects_mapper_test_version_mock',
                        array('_do_parent_insert',
                              '_do_parent_update',
                              '_do_parent_delete',
                              '_get_finder',
                              '_do_load'));
Mock :: generate('user');
Mock :: generate('versioned_one_table_objects_raw_finder');

class versioned_one_table_objects_mapper_test_news_object extends versioned_site_object
{
  public function get_annotation()
  {
    return $this->get('annotation');
  }

  public function set_annotation($annotation)
  {
    $this->set('annotation', $annotation);
  }

  public function get_content()
  {
    return $this->get('content');
  }

  public function set_content($content)
  {
    $this->set('content', $content);
  }

  public function get_news_date()
  {
    return $this->get('news_date');
  }

  public function set_news_date($news_date)
  {
    $this->set('news_date', $news_date);
  }
}

class versioned_one_table_objects_mapper_test extends LimbTestCase
{
  var $db;
  var $mapper;
  var $toolkit;

  function setUp()
  {
    $this->db = db_factory :: instance();

    $this->_clean_up();

    $this->user = new Mockuser($this);
    $this->user->setReturnValue('get_id', 125);

    $this->toolkit = new VersionedOneTableObjectsTestToolkitMock($this);
    $this->toolkit->setReturnValue('getUser', $this->user);
    Limb :: registerToolkit($this->toolkit);

    $this->mapper = new versioned_one_table_objects_mapper_test_version_mock($this);
  }

  function tearDown()
  {
    $this->mapper->tally();
    $this->toolkit->tally();

    Limb :: popToolkit();

    $this->_clean_up();
  }

  function _clean_up()
  {
    $this->db->sql_delete('sys_object_version');
    $this->db->sql_delete('test_one_table_object');
  }

  function test_get_db_table()
  {
    $this->assertIsA($this->mapper->get_db_table(), 'one_table_objects_mapper_test_version_db_table');
  }

  function test_insert()
  {
    $site_object = new versioned_one_table_objects_mapper_test_news_object();
    $site_object->set_id($object_id = 100);
    $site_object->set_version(1);
    $site_object->set_creator_id(1);
    $site_object->set_created_date(1);
    $site_object->set_modified_date(1);

    $this->mapper->expectOnce('_do_parent_insert', array($site_object));

    $this->mapper->insert($site_object);

    $this->assertTrue($site_object->get_created_date() >= time());
    $this->assertTrue($site_object->get_modified_date() >= time());
    $this->assertEqual($site_object->get_creator_id(), $this->user->get_id());

    $this->_check_sys_object_version_record($site_object);
  }

  function test_versioned_update()
  {
    $site_object = new versioned_one_table_objects_mapper_test_news_object();

    $site_object->set_id($object_id = 100);
    $site_object->set_version(1);
    $site_object->increase_version();

    $this->db->sql_insert('test_one_table_object', array(
                                                   'object_id' => $object_id,
                                                   'identifier' => 'test',
                                                   'title' => 'Title',
                                                   'annotation' => 'news annotation',
                                                   'content' => 'news content',
                                                   'news_date' => '2000-01-02 00:00:00',
                                                   'version' => 1));

    $this->mapper->expectOnce('_do_parent_update', array($site_object));

    $site_object->set_identifier('test2');
    $site_object->set_title('Title2');
    $site_object->set_annotation('news annotation2');
    $site_object->set_content('news content2');
    $site_object->set_news_date('2004-01-02 00:00:00');

    $this->mapper->update($site_object);

    $this->assertEqual($site_object->get_version(), 2);

    $this->db->sql_select('test_one_table_object');
    $this->assertEqual(sizeof($this->db->get_array()), 2);

    $this->_check_linked_table_record($site_object);
    $this->_check_sys_object_version_record($site_object);
  }

  function test_nonversioned_update_ok()
  {
    $site_object = new versioned_one_table_objects_mapper_test_news_object();

    $site_object->set_id($object_id = 100);
    $site_object->set_version($version = 1);
    $site_object->set_creator_id($creator_id = 100);
    $site_object->set_created_date($created_date = 10);
    $site_object->set_modified_date($modified_date = 10);

    $this->db->sql_insert('sys_object_version', array(
                                                   'object_id' => $object_id,
                                                   'creator_id' => $creator_id,
                                                   'created_date' => $created_date,
                                                   'modified_date' => $modified_date,
                                                   'version' => $version));


    $this->mapper->expectOnce('_do_parent_update', array($site_object));

    $this->mapper->update($site_object);

    $this->assertEqual($site_object->get_created_date(), $created_date);
    $this->assertTrue($site_object->get_modified_date() >= time() - 60);

    $this->_check_sys_object_version_record($site_object);
  }

  function test_find_by_version_failed()
  {
    $finder = new Mockversioned_one_table_objects_raw_finder($this);
    $finder->setReturnValue('find_by_version', array(), array($object_id  = 10, $version = 10000));

    $this->mapper->setReturnValue('_get_finder', $finder);

    $this->assertNull($this->mapper->find_by_version($object_id, $version));
  }

  function test_find_by_version_ok()
  {
    $finder = new Mockversioned_one_table_objects_raw_finder($this);

    $result = array('id' => $id = 10,
                    'identifier' => $identifier = 'test',
                    'current_version' => 4,
                    'version' => $version = 3,
                    'behaviour_id' => $behaviour_id = 100);

    $finder->setReturnValue('find_by_version', $result, array($id, $version));

    $this->mapper->setReturnValue('_get_finder', $finder);
    $this->mapper->expectOnce('_do_load', array($result, new IsAExpectation('versioned_one_table_objects_mapper_test_news_object')));
    $this->mapper->find_by_version($id, $version);
  }

  function test_delete()
  {
    $site_object = new versioned_one_table_objects_mapper_test_news_object();

    $site_object->set_id($object_id = 100);

    $this->db->sql_insert('sys_object_version', array(
                                                   'object_id' => $object_id,
                                                   'version' => 1));

    $this->db->sql_insert('sys_object_version', array(
                                                   'object_id' => $object_id,
                                                   'version' => 2));

    $this->db->sql_insert('sys_object_version', array(
                                                   'object_id' => $junk_object_id = 101,
                                                   'version' => 2));


    $this->mapper->expectOnce('_do_parent_delete', array($site_object));
    $this->mapper->delete($site_object);

    $this->db->sql_select('sys_object_version');
    $array = $this->db->get_array();

    $this->assertEqual(sizeof($array), 1);
    $this->assertEqual($array[0]['object_id'], $junk_object_id);
  }

  function test_trim_versions()
  {
    $this->db->sql_insert('sys_object_version', array(
                                                   'object_id' => $object_id = 100,
                                                   'version' => 1));

    $this->db->sql_insert('sys_object_version', array(
                                                   'object_id' => $object_id,
                                                   'version' => 3));

    // should not be deleted!
    $this->db->sql_insert('sys_object_version', array(
                                                   'object_id' => $junk_object_id = 101,
                                                   'version' => 2));

    $this->db->sql_insert('test_one_table_object', array(
                                                   'object_id' => $object_id,
                                                   'identifier' => 'test',
                                                   'title' => 'Title',
                                                   'annotation' => 'news annotation',
                                                   'content' => 'news content',
                                                   'news_date' => '2000-01-02 00:00:00',
                                                   'version' => 1));

    // should not be deleted!
    $this->db->sql_insert('test_one_table_object', array(
                                                   'object_id' => $junk_object_id,
                                                   'identifier' => 'test',
                                                   'title' => 'Title',
                                                   'annotation' => 'news annotation',
                                                   'content' => 'news content',
                                                   'news_date' => '2000-01-02 00:00:00',
                                                   'version' => 1));
    $this->mapper->trim_versions($object_id, $version = 2);

    $this->db->sql_select('sys_object_version');
    $array = $this->db->get_array();

    $this->assertEqual(sizeof($array), 1);
    $this->assertEqual($array[0]['object_id'], $junk_object_id);

    $this->db->sql_select('test_one_table_object');
    $array = $this->db->get_array();

    $this->assertEqual(sizeof($array), 1);
    $this->assertEqual($array[0]['object_id'], $junk_object_id);
  }

  function _check_sys_object_version_record($site_object)
  {
    $conditions['object_id'] = $site_object->get_id();
    $conditions['version'] = $site_object->get_version();

    $this->db->sql_select('sys_object_version', '*', $conditions);
    $record = $this->db->fetch_row();

    $this->assertEqual($record['object_id'], $site_object->get_id());
    $this->assertEqual($record['version'], $site_object->get_version());
    $this->assertEqual($record['creator_id'], $site_object->get_creator_id());
    $this->assertEqual($record['created_date'], $site_object->get_created_date());
    $this->assertEqual($record['modified_date'], $site_object->get_modified_date());
  }

  function _check_linked_table_record($site_object)
  {
    $conditions['object_id'] = $site_object->get_id();
    $conditions['version'] = $site_object->get_version();

    $db_table = $this->mapper->get_db_table();
    $arr = $db_table->get_list($conditions, 'id');

    $this->assertEqual(sizeof($arr), 1);
    $record = current($arr);

    $this->assertEqual($record['identifier'], $site_object->get_identifier());
    $this->assertEqual($record['title'], $site_object->get_title());
    $this->assertEqual($record['annotation'], $site_object->get_annotation());
    $this->assertEqual($record['content'], $site_object->get_content());
    $this->assertEqual($record['news_date'], $site_object->get_news_date());
  }
}

?>