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
require_once(LIMB_DIR . '/core/commands/DeleteSiteObjectCommand.class.php');
require_once(LIMB_DIR . '/core/request/Request.class.php');
require_once(LIMB_DIR . '/core/LimbToolkit.interface.php');
require_once(LIMB_DIR . '/core/daos/RequestedObjectDAO.class.php');
require_once(LIMB_DIR . '/core/site_objects/SiteObject.class.php');

Mock :: generate('LimbToolkit');
Mock :: generate('Request');
Mock :: generate('RequestedObjectDAO');
Mock :: generate('SiteObject');

class SiteObjectDeleteCommandTestVersion1 extends SiteObject
{
  function delete()
  {
    return throw(new LimbException('catch me!'));
  }
}

class SiteObjectDeleteCommandTestVersion2 extends SiteObject
{
  function delete()
  {
    return throw(new SQLException('catch me!'));
  }
}


class DeleteSiteObjectCommandTest extends LimbTestCase
{
  var $delete_command;
  var $site_object;
  var $request;
  var $toolkit;
  var $dao;

  function DeleteSiteObjectCommandTest()
  {
    parent :: LimbTestCase('delete site object cmd test');
  }

  function setUp()
  {
    $this->request = new MockRequest($this);
    $this->dao = new MockRequestedObjectDAO($this);
    $this->site_object = new MockSiteObject($this);

    $this->toolkit = new MockLimbToolkit($this);
    $this->toolkit->setReturnReference('createDAO', $this->dao, array('RequestedObjectDAO'));
    $this->toolkit->setReturnReference('getRequest', $this->request);

    Limb :: registerToolkit($this->toolkit);

    $this->delete_command = new DeleteSiteObjectCommand();
  }

  function tearDown()
  {
    Limb :: popToolkit();

    $this->request->tally();
    $this->dao->tally();
    $this->toolkit->tally();
    $this->site_object->tally();
  }

  function testDeleteOk()
  {
    $object_data = array('class_name' => 'some_class');

    $this->dao->expectOnce('setRequest', array(new IsAExpectation('MockRequest')));
    $this->dao->expectOnce('fetch');
    $this->dao->setReturnValue('fetch', $object_data);

    $this->toolkit->setReturnReference('createSiteObject', $this->site_object);

    $this->site_object->expectOnce('delete');
    $this->assertEqual($this->delete_command->perform(), LIMB_STATUS_OK);
  }

  function testDeleteFailed()
  {
    $this->toolkit->setReturnReference('createSiteObject', new SiteObjectDeleteCommandTestVersion1());

    $object_data = array('class_name' => 'some_class');

    $this->dao->expectOnce('setRequest', array(new IsAExpectation('MockRequest')));
    $this->dao->expectOnce('fetch');
    $this->dao->setReturnValue('fetch', $object_data);

    $this->assertEqual($this->delete_command->perform(), LIMB_STATUS_ERROR);
  }

  function testDeleteFailedSqlException()
  {
    $this->toolkit->setReturnReference('createSiteObject', new SiteObjectDeleteCommandTestVersion2());

    $object_data = array('class_name' => 'some_class');

    $this->dao->expectOnce('setRequest', array(new IsAExpectation('MockRequest')));
    $this->dao->expectOnce('fetch');
    $this->dao->setReturnValue('fetch', $object_data);

    $this->delete_command->perform();
    $this->assertTrue(catch('Exception', $e));
  }

}

?>