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
require_once(dirname(__FILE__) . '/../../../DAO/SimpleACLActionsRecordSet.class.php');
require_once(dirname(__FILE__) . '/../../../SimpleACLAuthorizer.class.php');
require_once(dirname(__FILE__) . '/../../../SimpleACLBaseToolkit.class.php');

Mock :: generate('SimpleACLAuthorizer');

class SimpleACLActionsRecordSetTest extends LimbTestCase
{
  function SimpleACLActionsRecordSetTest()
  {
    parent :: LimbTestCase('simple ACL actions record set test');
  }

  function setUp()
  {
    Limb :: registerToolkit(new SimpleACLBaseToolkit(), 'SimpleACL');
  }

  function tearDown()
  {
    Limb :: popToolkit('SimpleACL');
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
    $services = array($service1 = array('whatever1'),
                     $service2 = array('whatever2'));

    $rs = new SimpleACLActionsRecordSet(new PagedArrayDataSet($services));

    $authorizer = new MockSimpleACLAuthorizer($this);
    $rs->setAuthorizer($authorizer);

    $authorizer->expectArgumentsAt(0, 'assignActions', array(new IsAExpectation('DataSpace')));
    $authorizer->expectCallCount('assignActions', 2);

    $rs->rewind();

    $rs->current();
    $rs->next();
    $rs->current();

    $authorizer->tally();
  }
}

?>