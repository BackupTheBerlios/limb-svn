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
require_once(LIMB_DIR . '/class/core/data_mappers/OneTableObjectsMapper.class.php');
require_once(LIMB_DIR . '/class/core/site_objects/SiteObject.class.php');
require_once(dirname(__FILE__) . '/OneTableObjectsMapperTestVersionDbTable.class.php');
require_once(LIMB_DIR . '/class/core/finders/OneTableObjectsRawFinder.class.php');

class OneTableObjectsMapperTestVersion extends OneTableObjectsMapper
{
  function _createDomainObject()
  {
    return new OneTableObjectsMapperTestNewsObject();
  }

  function _defineDbTableName()
  {
    return 'OneTableObjectsMapperTestVersion';
  }
}

Mock :: generatePartial('OneTableObjectsMapperTestVersion',
                        'OneTableObjectsMapperTestVersionMock',
                        array('_doParentInsert',
                              '_doParentUpdate',
                              '_doParentDelete',
                              '_getFinder',
                              '_doLoadBehaviour'));

Mock :: generate('OneTableObjectsRawFinder');

class OneTableObjectsMapperTestNewsObject extends SiteObject
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

class OneTableObjectsMapperTest extends LimbTestCase
{
  var $db;
  var $mapper;

  function setUp()
  {
    $this->db =& DbFactory :: instance();

    $this->_cleanUp();

    $this->mapper = new OneTableObjectsMapperTestVersionMock($this);
  }

  function tearDown()
  {
    $this->mapper->tally();

    $this->_cleanUp();
  }

  function _cleanUp()
  {
    $this->db->sqlDelete('test_one_table_object');
  }

  function testGetDbTable()
  {
    $this->assertIsA($this->mapper->getDbTable(), 'OneTableObjectsMapperTestVersionDbTable');
  }

  function testFindById()
  {
    $finder = new MockOneTableObjectsRawFinder($this);
    $result = array('id' => $id = 10,
                    'identifier' => $identifier = 'test',
                    'behaviour_id' => $behaviour_id = 100,
                    'content' => $content = 'some content',
                    'annotation' => $annotation = 'some annotation',
                    'object_id' => $id,
                    'news_date' => $news_date = 'some date');

    $finder->expectOnce('findById', array($id));
    $finder->setReturnValue('findById', $result, array($id));

    $this->mapper->setReturnReference('_getFinder', $finder);
    $this->mapper->expectOnce('_doLoadBehaviour',
                              array($result,
                                    new IsAExpectation('OneTableObjectsMapperTestNewsObject')));

    $site_object = $this->mapper->findById($id);

    $this->assertEqual($site_object->getId(), $id);
    $this->assertEqual($site_object->getIdentifier(), $identifier);
    $this->assertEqual($site_object->getContent(), $content);
    $this->assertEqual($site_object->getAnnotation(), $annotation);
    $this->assertEqual($site_object->getNewsDate(), $news_date);

    $finder->tally();
  }

  function testInsertExtraTableRecordOk()
  {
    $site_object = new OneTableObjectsMapperTestNewsObject();

    //we do it because we mocked parent create call
    $site_object->setId($new_object_id = 100);

    $this->mapper->expectOnce('_doParentInsert', array($site_object));
    $this->mapper->setReturnValue('_doParentInsert', $new_object_id);

    $site_object->setIdentifier('test');
    $site_object->setTitle('Title');
    $site_object->setAnnotation('news annotation');
    $site_object->setContent('news content');
    $site_object->setNewsDate('2004-01-02 00:00:00');
    $site_object->setCreatorId(124);
    $this->assertEqual($new_object_id, $this->mapper->insert($site_object));

    $this->_checkLinkedTableRecord($site_object);
  }

  function testUpdate()
  {
    $site_object = new OneTableObjectsMapperTestNewsObject();
    //we do it because we mock parent update call
    $site_object->setId($object_id = 100);

    $this->db->sqlInsert('test_one_table_object',
                         array('object_id' => $object_id,
                               'identifier' => 'test',
                               'title' => 'Title',
                               'annotation' => 'news annotation',
                               'content' => 'news content',
                               'news_date' => '2000-01-02 00:00:00'));

    $this->mapper->expectOnce('_doParentUpdate', array($site_object));

    $site_object->set('identifier', 'test2');
    $site_object->set('title', 'Title2');
    $site_object->set('annotation', 'news annotation2');
    $site_object->set('content', 'news content2');
    $site_object->set('news_date', '2004-01-02 00:00:00');

    $this->mapper->update($site_object);

    $this->db->sqlSelect('test_one_table_object');
    $this->assertEqual(sizeof($this->db->getArray()), 1);

    $this->_checkLinkedTableRecord($site_object);
  }

  function testDelete()
  {
    $site_object = new OneTableObjectsMapperTestNewsObject();

    $site_object->setId($object_id = 100);

    $this->db->sqlInsert('test_one_table_object', array(
                                                   'object_id' => $object_id,
                                                   'identifier' => 'test',
                                                   'title' => 'Title',
                                                   'annotation' => 'news annotation',
                                                   'content' => 'news content',
                                                   'news_date' => '2000-01-02 00:00:00',
                                                   'version' => 1));

    $this->mapper->expectOnce('_doParentDelete', array($site_object));
    $this->mapper->delete($site_object);

    $this->db->sqlSelect('test_one_table_object');
    $this->assertEqual(sizeof($this->db->getArray()), 0);
  }

  function _checkLinkedTableRecord($site_object)
  {
    $conditions['object_id'] = $site_object->getId();

    $db_table = $this->mapper->getDbTable();
    $arr = $db_table->getList($conditions, 'id');

    $this->assertEqual(sizeof($arr), 1);
    $record = current($arr);

    $this->assertEqual($record['identifier'], $site_object->getIdentifier());
    $this->assertEqual($record['title'], $site_object->getTitle());
    $this->assertEqual($record['annotation'], $site_object->getAnnotation());
    $this->assertEqual($record['content'], $site_object->getContent());
    $this->assertEqual($record['news_date'], $site_object->getNewsDate());
  }
}

?>