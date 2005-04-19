<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: BehaviourTest.class.php 1161 2005-03-14 16:55:07Z pachanga $
*
***********************************************************************************/
require_once(LIMB_SERVICE_NODE_DIR . '/dao/BreadcrumbsDAO.class.php');
require_once(LIMB_DIR . '/core/db/SimpleDb.class.php');
require_once(LIMB_DIR . '/core/LimbBaseToolkit.class.php');
require_once(LIMB_DIR . '/core/UnitOfWork.class.php');
require_once(LIMB_DIR . '/core/tree/Tree.interface.php');
require_once(LIMB_DIR . '/core/tree/Path2IdTranslator.class.php');

Mock :: generatePartial('LimbBaseToolkit',
                        'LimbBaseToolkitObjectMetadataDAOTestVersion',
                        array('getUOW'));

Mock :: generate('UnitOfWork');
Mock :: generate('Path2IdTranslator');
Mock :: generatePartial('BreadcrumbsDAO',
                        'BreadcrumbsDAOTestVersion',
                        array('getPath2IdTranslator'));

class BreadcrumbsDAOTest extends LimbTestCase
{
  var $toolkit;
  var $uow;
  var $translator;

  function BreadcrumbsDAOTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    $toolkit =& Limb :: toolkit();
    $this->conn =& $toolkit->getDBConnection();
    $this->db = new SimpleDB($this->conn);

    $this->uow = new MockUnitOfWork($this);

    $this->toolkit = new LimbBaseToolkitObjectMetadataDAOTestVersion($this);
    $this->toolkit->setReturnReference('getUOW', $this->uow);

    $this->translator = new MockPath2IdTranslator($this);

    Limb :: registerToolkit($this->toolkit);
    $this->_cleanUp();
  }

  function _cleanUp()
  {
    $this->db->delete('sys_object');
    $this->db->delete('sys_class');
  }

  function tearDown()
  {
    $this->uow->tally();
    $this->toolkit->tally();
    $this->translator->tally();

    Limb :: restoreToolkit();

    $this->_cleanUp();
  }

  function testFetchAllByPath()
  {
    $toolkit =& Limb :: toolkit();
    $request =& $toolkit->getRequest();
    $uri =& $request->getUri();
    $uri->setPath($path = '/about/news/1');

    $object1 = new Dataspace();
    $object1->set('title', $title1 = 'title1');
    $object1->set('path', $path1 = '/about/news/1');
    $object2 = new Dataspace();
    $object2->set('title', $title2 = 'title2');
    $object2->set('path', $path2 = '/about/news');
    $object3 = new Dataspace();
    $object3->set('title', $title3 = 'title1');
    $object3->set('path', $path3 = '/about');


    $this->db->insert('sys_object', array('oid' => $oid1 = 100, 'class_id' => $class_id1 = 1));
    $this->db->insert('sys_object', array('oid' => $oid2 = 101, 'class_id' => $class_id2 = 2));
    $this->db->insert('sys_object', array('oid' => $oid3 = 102, 'class_id' => $class_id3 = 3));
    $this->db->insert('sys_class', array('id' => $class_id1, 'name' => $class_name1 = 'class_name1'));
    $this->db->insert('sys_class', array('id' => $class_id2, 'name' => $class_name2 = 'class_name2'));
    $this->db->insert('sys_class', array('id' => $class_id3, 'name' => $class_name3 = 'class_name3'));

    $this->translator->expectArgumentsAt(0, 'toId', array($path3 = '/about'));
    $this->translator->setReturnValueAt(0, 'toId', $oid3);
    $this->translator->expectArgumentsAt(1, 'toId', array($path2 = '/about/news'));
    $this->translator->setReturnValueAt(1, 'toId', $oid2);
    $this->translator->expectArgumentsAt(2, 'toId', array($path1));
    $this->translator->setReturnValueAt(2, 'toId', $oid1);
    $this->translator->expectCallCount('toId' , 3);

    $dao = new BreadcrumbsDAOTestVersion($this);
    $dao->setReturnReference('getPath2IdTranslator', $this->translator);

    $this->uow->expectArgumentsAt(0, 'load', array($class_name3, $oid3));
    $this->uow->setReturnValueAt(0, 'load', $object3);
    $this->uow->expectArgumentsAt(1, 'load', array($class_name2, $oid2));
    $this->uow->setReturnValueAt(1, 'load', $object2);
    $this->uow->expectArgumentsAt(2, 'load', array($class_name1, $oid1));
    $this->uow->setReturnValueAt(2, 'load', $object1);
    $this->uow->expectCallCount('load' , 3);

    $rs =& $dao->fetch();
    $this->assertEqual($rs->getTotalRowCount(), 3);

    $rs->rewind();
    $record =& $rs->current();
    $this->assertEqual($record->get('title'), $title3);
    $this->assertEqual($record->get('path'), $path3);

    $rs->next();
    $record =& $rs->current();
    $this->assertEqual($record->get('title'), $title2);
    $this->assertEqual($record->get('path'), $path2);

    $rs->next();
    $record =& $rs->current();
    $this->assertEqual($record->get('title'), $title1);
    $this->assertEqual($record->get('path'), $path1);

  }

  function testGetPath2IdTranslator()
  {
    $dao = new BreadcrumbsDAO();
    $this->assertIsA($dao->getPath2IdTranslator(), 'Path2IdTranslator');
  }

}

?>