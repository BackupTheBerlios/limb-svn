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

class SimpleACLAccessRecordSetTest extends LimbTestCase
{
  function SimpleACLAccessRecordSetTest()
  {
    parent :: LimbTestCase('simple ACL access record set test');
  }

  function setUp()
  {
  }

  function tearDown()
  {
  }

  function testEmpty()
  {
    $rs = new SimpleACLAccessRecordSet(new PagedArrayDataset(array()));
    $rs->rewind();
    $this->assertFalse($rs->valid());
  }

  function testFindWithVariations()
  {
    $authenticator = new MockSimpleACLAuthorizer();
    $authenticator->expectOnce('');

    $objects = array(array('path' => '/root'),
                     array('path' => '/root/admin'),
                     array('path' => '/root/news'),
                     array('path' => '/root/admin/access'),
                     array('path' => '/root/documents/doc1'));

    $rs = new SimpleACLAccessRecordSet(new PagedArrayDataSet($objects));
    $this->assertEqual($rs->getRowCount(), 2);
    $this->assertEqual($rs->getTotalRowCount(), 2);

    $rs->rewind();

    $rs->next();
    $record = $rs->current();
    $this->assertEqual($record->get('path'), '/root');

    $rs->next();
    $record = $rs->current();
    $this->assertEqual($record->get('path'), '/root/documents/doc1');

    $rs->next();
    $this->assertFalse($rs->valid());
  }
}

?>