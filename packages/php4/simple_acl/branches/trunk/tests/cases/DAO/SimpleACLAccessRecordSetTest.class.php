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
require_once(dirname(__FILE__) . '/../../../DAO/SimpleACLAccessRecordSet.class.php');
require_once(dirname(__FILE__) . '/../../../SimpleACLAuthorizer.class.php');

Mock :: generate('SimpleACLAuthorizer');

class SimpleACLAccessRecordSetTest extends LimbTestCase
{
  function SimpleACLAccessRecordSetTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function testEmpty()
  {
    $rs = new SimpleACLAccessRecordSet(new PagedArrayDataset(array()));

    $authorizer = new MockSimpleACLAuthorizer($this);
    $rs->setAuthorizer($authorizer);

    $rs->rewind();
    $this->assertFalse($rs->valid());
  }

  function testApplyAccess()
  {
    $services = array($service1 = array('whatever1'),
                     $service2 = array('whatever2'));

    $rs = new SimpleACLAccessRecordSet(new PagedArrayDataSet($services));
    $rs->setAction($action = 'delete');

    $authorizer = new MockSimpleACLAuthorizer($this);
    $rs->setAuthorizer($authorizer);

    $authorizer->setReturnValueAt(0, 'canDo', true, array($action, new IsAExpectation('DataSpace')));
    $authorizer->setReturnValueAt(1, 'canDo', false, array($action, new IsAExpectation('DataSpace')));

    $this->assertEqual($rs->getRowCount(), 2);
    $this->assertEqual($rs->getTotalRowCount(), 2);

    $rs->rewind();

    $record = $rs->current();
    $this->assertEqual($record->get('is_accessible'), true);

    $rs->next();
    $record = $rs->current();
    $this->assertEqual($record->get('is_accessible'), false);

    $authorizer->tally();
  }
}

?>