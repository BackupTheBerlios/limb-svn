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
require_once(LIMB_DIR . '/core/db/LimbDbPool.class.php');
require_once(LIMB_DIR . '/core/data_mappers/OneTableObjectMapper.class.php');
require_once(LIMB_DIR . '/core/DomainObject.class.php');
require_once(dirname(__FILE__) . '/OneTableObjectMapperTestDbTable.class.php');

class OneTableObjectMapperTestNewsObject extends DomainObject
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
    parent :: LimbTestCase('one table object mapper test');
  }

  function setUp()
  {
    $this->db =& new SimpleDb(LimbDbPool :: getConnection());

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
    $domain_object = new OneTableObjectMapperTestNewsObject();

    $result = array('id' => $id = 10,
                    'content' => $content = 'some content',
                    'annotation' => $annotation = 'some annotation',
                    'news_date' => $news_date = 'some date',
                    'junk' => 'junk!!!');

    $record = new Dataspace();
    $record->import($result);

    $mapper->load($record, $domain_object);

    $this->assertEqual($domain_object->getId(), $id);
    $this->assertEqual($domain_object->getContent(), $content);
    $this->assertEqual($domain_object->getAnnotation(), $annotation);
    $this->assertEqual($domain_object->getNewsDate(), $news_date);

    $this->assertNull($domain_object->get('junk'));
  }

  function testInsertExtraTableRecordOk()
  {
    $mapper = new OneTableObjectMapper('OneTableObjectMapperTest');
    $domain_object = new OneTableObjectMapperTestNewsObject();

    $domain_object->setAnnotation('news annotation');
    $domain_object->setContent('news content');
    $domain_object->setNewsDate('2004-01-02 00:00:00');

    $mapper->insert($domain_object);

    $this->assertEqual($domain_object->getId(), 1);

    $this->_checkLinkedTableRecord($domain_object, $mapper->getDbTable());
  }

  function testUpdate()
  {
    $mapper = new OneTableObjectMapper('OneTableObjectMapperTest');
    $domain_object = new OneTableObjectMapperTestNewsObject();

    $domain_object->setId($object_id = 100);

    $this->db->insert('test_one_table_object',
                         array('id' => $object_id,
                               'annotation' => 'news annotation',
                               'content' => 'news content',
                               'news_date' => '2000-01-02 00:00:00'));


    $domain_object->setAnnotation('news annotation2');
    $domain_object->setContent('news content2');
    $domain_object->setNewsDate('2004-01-02 00:00:00');

    $mapper->update($domain_object);

    $rs =& $this->db->select('test_one_table_object');
    $this->assertEqual(sizeof($rs->getTotalRowCount()), 1);

    $this->_checkLinkedTableRecord($domain_object, $mapper->getDbTable());
  }

  function testDelete()
  {
    $mapper = new OneTableObjectMapper('OneTableObjectMapperTest');
    $domain_object = new OneTableObjectMapperTestNewsObject();

    $domain_object->setId($object_id = 100);

    $this->db->insert('test_one_table_object',
                      array('id' => $object_id,
                           'annotation' => 'news annotation',
                           'content' => 'news content',
                           'news_date' => '2000-01-02 00:00:00'));

    // this record must stay
    $this->db->insert('test_one_table_object',
                      array('id' => 102,
                           'annotation' => 'news annotation2',
                           'content' => 'news content2',
                           'news_date' => '2000-01-03 00:00:00'));

    $mapper->delete($domain_object);

    $rs =& $this->db->select('test_one_table_object');
    $this->assertEqual(sizeof($rs->getArray()), 1);
  }

  function _checkLinkedTableRecord($domain_object, $db_table)
  {
    $conditions['id'] = $domain_object->getId();

    $rs =& $db_table->select($conditions, 'id');
    $arr = $rs->getArray();

    $this->assertEqual(sizeof($arr), 1);
    $record = current($arr);

    $this->assertEqual($record['annotation'], $domain_object->getAnnotation());
    $this->assertEqual($record['content'], $domain_object->getContent());
    $this->assertEqual($record['news_date'], $domain_object->getNewsDate());
  }
}

?>