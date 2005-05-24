<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: ImageObjectsDAOTest.class.php 1093 2005-02-07 15:17:20Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/dao/RequestUriPathElementsDAO.class.php');

class RequestUriPathElementsDAOTest extends LimbTestCase
{
  function RequestUriPathElementsDAOTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    Limb :: saveToolkit();
    $this->toolkit =& Limb :: toolkit();
  }

  function tearDown()
  {
    Limb :: restoreToolkit();
  }

  function testFetch()
  {
    $request =& $this->toolkit->getRequest();
    $uri =& $request->getUri();
    $uri->parse('http://test.com/root/level1/me?action=whatever');

    $dao = new RequestUriPathElementsDAO();

    $rs =& $dao->fetch();

    $rs->rewind();
    $record = $rs->current();
    $this->assertEqual($record->get('uri'), 'http://test.com/root/level1/');

    $rs->next();
    $record = $rs->current();
    $this->assertEqual($record->get('uri'), 'http://test.com/root/');

    $rs->next();
    $record = $rs->current();
    $this->assertEqual($record->get('uri'), 'http://test.com/');

    $rs->next();
    $this->assertFalse($rs->valid());

  }
}

?>