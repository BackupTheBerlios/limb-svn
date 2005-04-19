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
require_once(dirname(__FILE__) . '/../../../dao/SimpleACLActionsRecordSet.class.php');
require_once(dirname(__FILE__) . '/../../../SimpleACLAuthorizer.class.php');

Mock :: generate('SimpleACLAuthorizer');

class SimpleACLActionsRecordSetTest extends LimbTestCase
{
  function SimpleACLActionsRecordSetTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function testEmpty()
  {
    $rs = new SimpleACLActionsRecordSet(new PagedArrayDataset(array()));

    $authorizer = new MockSimpleACLAuthorizer($this);
    $rs->setAuthorizer($authorizer);

    $rs->rewind();
    $this->assertFalse($rs->valid());
  }

  function testApplyAccess()
  {
    $records = array(array('path' => $path1 = 'path1', '_service_name' => $service_name1 = 'service_name1'),
                     array('path' => $path2 = 'path2', '_service_name' => $service_name2 = 'service_name2'));

    $rs = new SimpleACLActionsRecordSet(new PagedArrayDataSet($records));

    $authorizer = new MockSimpleACLAuthorizer($this);
    $rs->setAuthorizer($authorizer);

    $authorizer->expectArgumentsAt(0, 'getAccessibleActions', array($path1, $service_name1));
    $authorizer->expectArgumentsAt(1, 'getAccessibleActions', array($path2, $service_name2));
    $authorizer->expectCallCount('getAccessibleActions', 2);

    $rs->rewind();

    $rs->current();
    $rs->next();
    $rs->current();

    $authorizer->tally();
  }
}

?>