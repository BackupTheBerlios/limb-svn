<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: ImageObjectsRawFinderTest.class.php 1091 2005-02-03 13:10:12Z pachanga $
*
***********************************************************************************/
require_once(WACT_ROOT . '/iterator/pagedarraydataset.inc.php');
require_once(LIMB_DIR . '/core/LimbBaseToolkit.class.php');
require_once(LIMB_DIR . '/core/Object.class.php');
require_once(LIMB_DIR . '/core/tree/Path2IdTranslator.class.php');
require_once(LIMB_DIR . '/core/DAO/ChildItemsPathAssignerRecordSet.class.php');

Mock :: generatePartial('LimbBaseToolkit',
                 'LimbBaseToolkitChildItemsPathAssignerRecordSetTestVersion',
                 array('getPath2IdTranslator'));

Mock :: generate('Path2IdTranslator');

class ChildItemsPathAssignerRecordSetTest extends LimbTestCase
{
  var $path2id_translator;
  var $toolkit;

  function ChildItemsPathAssignerRecordSetTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    $this->toolkit = new LimbBaseToolkitChildItemsPathAssignerRecordSetTestVersion($this);
    $this->path2id_translator = new MockPath2IdTranslator($this);
    $this->toolkit->setReturnReference('getPath2IdTranslator', $this->path2id_translator);

    Limb :: registerToolkit($this->toolkit);
  }

  function tearDown()
  {
    $this->toolkit->tally();
    $this->path2id_translator->tally();

    Limb :: restoreToolkit();
  }

  function testCurrent()
  {
    $object = new Object();
    $object->set('oid', $id = 5);
    $this->toolkit->setCurrentEntity($object);

    $this->path2id_translator->expectOnce('toPath', array($id));
    $this->path2id_translator->setReturnValue('toPath', $path = 'whatever');

    $objects = array(array('identifier' => 'item1'),
                    array('identifier' => 'item2'));

    $rs = new ChildItemsPathAssignerRecordSet(new PagedArrayDataset($objects));
    $rs->rewind();

    $record =& $rs->current();

    $this->assertEqual($record->get('path'), "{$path}/" . $objects[0]['identifier']);

    $rs->next();
    $record =& $rs->current();
    $this->assertEqual($record->get('path'), "{$path}/" . $objects[1]['identifier']);
  }
}

?>