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
require_once(LIMB_DIR . '/core/DAO/JIPActionsRecordSet.class.php');

class JIPActionsRecordSetTest extends LimbTestCase
{
  function JIPActionsRecordSetTest()
  {
    parent :: LimbTestCase('JIP actions record set test');
  }

  function testCurrent()
  {
    $data = array(array('path' => $path1 = '/cms/limb/',
                        'actions' => array('create' => array('jip' => true),
                                            'edit' => array('jip' => true),
                                            'display' => array(),
                                            'delete' => array('jip' => true))),
                  array());

    $rs = new JIPActionsRecordSet(new PagedArrayDataset($data));

    $rs->rewind();
    $record =& $rs->current();
    $actions = $record->get('actions');
    $this->assertEqual($actions['create']['jip_href'], "{$path1}?action=create");
    $this->assertEqual($actions['edit']['jip_href'], "{$path1}?action=edit");
    $this->assertEqual($actions['delete']['jip_href'], "{$path1}?action=delete");
    $this->assertTrue(empty($actions['display']['jip_href']));

    $rs->next();
    $record =& $rs->current();
    $this->assertFalse($record->get('actions'));
  }
}

?>