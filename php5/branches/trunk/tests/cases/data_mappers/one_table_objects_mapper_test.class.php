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
require_once(LIMB_DIR . '/class/core/data_mappers/one_table_objects_mapper.class.php');
require_once(LIMB_DIR . '/class/core/site_objects/site_object.class.php');
require_once(dirname(__FILE__) . '/one_table_objects_mapper_test_version_db_table.class.php');
require_once(LIMB_DIR . '/class/core/finders/one_table_objects_raw_finder.class.php');

class one_table_objects_mapper_test_version extends one_table_objects_mapper
{
  protected function _create_domain_object()
  {
    return new one_table_objects_mapper_test_news_object();
  }

  protected function _define_db_table_name()
  {
    return 'one_table_objects_mapper_test_version';
  }
}

Mock :: generatePartial('one_table_objects_mapper_test_version',
                        'one_table_objects_mapper_test_version_mock',
                        array('_do_parent_insert',
                              '_do_parent_update',
                              '_do_parent_delete',
                              '_get_finder',
                              '_do_load_behaviour'));

Mock :: generate('one_table_objects_raw_finder');

class one_table_objects_mapper_test_news_object extends site_object
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

class one_table_objects_mapper_test extends LimbTestCase
{
  var $db;
  var $mapper;

  function setUp()
  {
    $this->db = db_factory :: instance();

    $this->_clean_up();

    $this->mapper = new one_table_objects_mapper_test_version_mock($this);
  }

  function tearDown()
  {
    $this->mapper->tally();

    $this->_clean_up();
  }

  function _clean_up()
  {
    $this->db->sql_delete('test_one_table_object');
  }

  function test_get_db_table()
  {
    $this->assertIsA($this->mapper->get_db_table(), 'one_table_objects_mapper_test_version_db_table');
  }

  function test_find_by_id()
  {
    $finder = new Mockone_table_objects_raw_finder($this);
    $result = array('id' => $id = 10,
                    'identifier' => $identifier = 'test',
                    'behaviour_id' => $behaviour_id = 100,
                    'content' => $content = 'some content',
                    'annotation' => $annotation = 'some annotation',
                    'object_id' => $id,
                    'news_date' => $news_date = 'some date');

    $finder->expectOnce('find_by_id', array($id));
    $finder->setReturnValue('find_by_id', $result, array($id));

    $this->mapper->setReturnValue('_get_finder', $finder);
    $this->mapper->expectOnce('_do_load_behaviour',
                              array($result,
                                    new IsAExpectation('one_table_objects_mapper_test_news_object')));

    $site_object = $this->mapper->find_by_id($id);

    $this->assertEqual($site_object->get_id(), $id);
    $this->assertEqual($site_object->get_identifier(), $identifier);
    $this->assertEqual($site_object->get_content(), $content);
    $this->assertEqual($site_object->get_annotation(), $annotation);
    $this->assertEqual($site_object->get_news_date(), $news_date);

    $finder->tally();
  }

  function test_insert_extra_table_record_ok()
  {
    $site_object = new one_table_objects_mapper_test_news_object();

    //we do it because we mocked parent create call
    $site_object->set_id($new_object_id = 100);

    $this->mapper->expectOnce('_do_parent_insert', array($site_object));
    $this->mapper->setReturnValue('_do_parent_insert', $new_object_id);

    $site_object->set_identifier('test');
    $site_object->set_title('Title');
    $site_object->set_annotation('news annotation');
    $site_object->set_content('news content');
    $site_object->set_news_date('2004-01-02 00:00:00');
    $site_object->set_creator_id(124);
    $this->assertEqual($new_object_id, $this->mapper->insert($site_object));

    $this->_check_linked_table_record($site_object);
  }

  function test_update()
  {
    $site_object = new one_table_objects_mapper_test_news_object();
    //we do it because we mock parent update call
    $site_object->set_id($object_id = 100);

    $this->db->sql_insert('test_one_table_object', array(
                                                   'object_id' => $object_id,
                                                   'identifier' => 'test',
                                                   'title' => 'Title',
                                                   'annotation' => 'news annotation',
                                                   'content' => 'news content',
                                                   'news_date' => '2000-01-02 00:00:00'));

    $this->mapper->expectOnce('_do_parent_update', array($site_object));

    $site_object->set('identifier', 'test2');
    $site_object->set('title', 'Title2');
    $site_object->set('annotation', 'news annotation2');
    $site_object->set('content', 'news content2');
    $site_object->set('news_date', '2004-01-02 00:00:00');

    $this->mapper->update($site_object);

    $this->db->sql_select('test_one_table_object');
    $this->assertEqual(sizeof($this->db->get_array()), 1);

    $this->_check_linked_table_record($site_object);
  }

  function test_delete()
  {
    $site_object = new one_table_objects_mapper_test_news_object();

    $site_object->set_id($object_id = 100);

    $this->db->sql_insert('test_one_table_object', array(
                                                   'object_id' => $object_id,
                                                   'identifier' => 'test',
                                                   'title' => 'Title',
                                                   'annotation' => 'news annotation',
                                                   'content' => 'news content',
                                                   'news_date' => '2000-01-02 00:00:00',
                                                   'version' => 1));

    $this->mapper->expectOnce('_do_parent_delete', array($site_object));
    $this->mapper->delete($site_object);

    $this->db->sql_select('test_one_table_object');
    $this->assertEqual(sizeof($this->db->get_array()), 0);
  }

  function _check_linked_table_record($site_object)
  {
    $conditions['object_id'] = $site_object->get_id();

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