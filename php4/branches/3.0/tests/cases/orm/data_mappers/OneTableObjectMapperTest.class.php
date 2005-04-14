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
require_once(LIMB_DIR . '/core/data_mappers/OneTableObjectMapper.class.php');
require_once(LIMB_DIR . '/core/Object.class.php');
require_once(dirname(__FILE__) . '/OneTableObjectMapperTestDbTable.class.php');

class OneTableObjectMapperTestNewsObject extends Object
{
  function getAnnotation()
  {
    return $this->get('annotation');
  }

  function setAnnotation($annotation)
  {
    $this->set('annotation', $annotation);
  }

  function getContent()
  {
    return $this->get('content');
  }

  function setContent($content)
  {
    $this->set('content', $content);
  }

  function getNewsDate()
  {
    return $this->get('news_date');
  }

  function setNewsDate($news_date)
  {
    $this->set('news_date', $news_date);
  }
}

class OneTableObjectMapperTest extends LimbTestCase
{
  var $db;

  function OneTableObjectMapperTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    $toolkit =& Limb :: toolkit();
    $this->db =& new SimpleDb($toolkit->getDbConnection());

    $this->_cleanUp();
  }

  function tearDown()
  {
    $this->_cleanUp();
  }

  function _cleanUp()
  {
    $this->db->delete('test_one_table_object');
    $this->db->delete('sys_uid');
  }

  function testGetDbTable()
  {
    $mapper = new OneTableObjectMapper('OneTableObjectMapperTest');
    $this->assertIsA($mapper->getDbTable(), 'OneTableObjectMapperTestDbTable');
  }

  function testLoad()
  {
    $mapper = new OneTableObjectMapper('OneTableObjectMapperTest');
    $object = new OneTableObjectMapperTestNewsObject();

    $result = array('_content_id' => $id = 10,
                    '_content_content' => $content = 'some content',
                    '_content_annotation' => $annotation = 'some annotation',
                    '_content_news_date' => $news_date = 'some date');

    $record = new Dataspace();
    $record->import($result);

    $mapper->load($record, $object);

    $this->assertEqual($object->get('id'), $id);
    $this->assertEqual($object->get('content'), $content);
    $this->assertEqual($object->get('annotation'), $annotation);
    $this->assertEqual($object->get('news_date'), $news_date);
  }

  function testInsertExtraTableRecordOk()
  {
    $mapper = new OneTableObjectMapper('OneTableObjectMapperTest');
    $object = new OneTableObjectMapperTestNewsObject();

    $object->setAnnotation('news annotation');
    $object->setContent('news content');
    $object->setNewsDate('2004-01-02 00:00:00');

    $mapper->insert($object);

    $this->assertEqual($object->get('id'), 1);

    $this->_checkLinkedTableRecord($object, $mapper->getDbTable());
  }

  function testUpdate()
  {
    $mapper = new OneTableObjectMapper('OneTableObjectMapperTest');
    $object = new OneTableObjectMapperTestNewsObject();

    $object->set('id', $id = 100);

    $this->db->insert('test_one_table_object',
                         array('id' => $id,
                               'annotation' => 'news annotation',
                               'content' => 'news content',
                               'news_date' => '2000-01-02 00:00:00'));


    $object->setAnnotation('news annotation2');
    $object->setContent('news content2');
    $object->setNewsDate('2004-01-02 00:00:00');

    $mapper->update($object);

    $rs =& $this->db->select('test_one_table_object');
    $this->assertEqual(sizeof($rs->getTotalRowCount()), 1);

    $this->_checkLinkedTableRecord($object, $mapper->getDbTable());
  }

  function testDelete()
  {
    $mapper = new OneTableObjectMapper('OneTableObjectMapperTest');
    $object = new OneTableObjectMapperTestNewsObject();

    $object->set('id', $id = 100);

    $this->db->insert('test_one_table_object',
                      array('id' => $id,
                           'annotation' => 'news annotation',
                           'content' => 'news content',
                           'news_date' => '2000-01-02 00:00:00'));

    // this record must stay
    $this->db->insert('test_one_table_object',
                      array('id' => 102,
                           'annotation' => 'news annotation2',
                           'content' => 'news content2',
                           'news_date' => '2000-01-03 00:00:00'));

    $mapper->delete($object);

    $rs =& $this->db->select('test_one_table_object');
    $this->assertEqual(sizeof($rs->getArray()), 1);
  }

  function _checkLinkedTableRecord($object, $db_table)
  {
    $conditions['id'] = $object->get('id');

    $rs =& $db_table->select($conditions, 'id');
    $arr = $rs->getArray();

    $this->assertEqual(sizeof($arr), 1);
    $record = current($arr);

    $this->assertEqual($record['annotation'], $object->getAnnotation());
    $this->assertEqual($record['content'], $object->getContent());
    $this->assertEqual($record['news_date'], $object->getNewsDate());
  }
}

?>