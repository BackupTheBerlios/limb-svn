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
require_once(LIMB_DIR . '/class/core/commands/DeleteSiteObjectCommand.class.php');
require_once(LIMB_DIR . '/class/core/request/Request.class.php');
require_once(LIMB_DIR . '/class/core/LimbToolkit.interface.php');
require_once(LIMB_DIR . '/class/core/datasources/RequestedObjectDatasource.class.php');
require_once(LIMB_DIR . '/class/core/site_objects/SiteObject.class.php');

Mock :: generate('LimbToolkit');
Mock :: generate('Request');
Mock :: generate('RequestedObjectDatasource');
Mock :: generate('SiteObject');

class SiteObjectDeleteCommandTestVersion1 extends SiteObject
{
  function delete()
  {
    throw new LimbException('catch me!');
  }
}

class SiteObjectDeleteCommandTestVersion2 extends SiteObject
{
  function delete()
  {
    throw new SQLException('catch me!');
  }
}


class DeleteSiteObjectCommandTest extends LimbTestCase
{
  var $delete_command;
  var $site_object;
  var $request;
  var $toolkit;
  var $datasource;

  function setUp()
  {
    $this->request = new MockRequest($this);
    $this->datasource = new MockRequestedObjectDatasource($this);
    $this->site_object = new MockSiteObject($this);

    $this->toolkit = new MockLimbToolkit($this);
    $this->toolkit->setReturnValue('getDatasource', $this->datasource, array('RequestedObjectDatasource'));
    $this->toolkit->setReturnValue('getRequest', $this->request);

    Limb :: registerToolkit($this->toolkit);

    $this->delete_command = new DeleteSiteObjectCommand();
  }

  function tearDown()
  {
    Limb :: popToolkit();

    $this->request->tally();
    $this->datasource->tally();
    $this->toolkit->tally();
    $this->site_object->tally();
  }

  function testDeleteOk()
  {
    $object_data = array('class_name' => 'some_class');

    $this->datasource->expectOnce('setRequest', array(new IsAExpectation('MockRequest')));
    $this->datasource->expectOnce('fetch');
    $this->datasource->setReturnValue('fetch', $object_data);

    $this->toolkit->setReturnValue('createSiteObject', $this->site_object);

    $this->site_object->expectOnce('delete');
    $this->assertEqual($this->delete_command->perform(), Limb :: getSTATUS_OK());
  }

  function testDeleteFailed()
  {
    $this->toolkit->setReturnValue('createSiteObject', new SiteObjectDeleteCommandTestVersion1());

    $object_data = array('class_name' => 'some_class');

    $this->datasource->expectOnce('setRequest', array(new IsAExpectation('MockRequest')));
    $this->datasource->expectOnce('fetch');
    $this->datasource->setReturnValue('fetch', $object_data);

    $this->assertEqual($this->delete_command->perform(), LIMB_STATUS_ERROR);
  }

  function testDeleteFailedSqlException()
  {
    $this->toolkit->setReturnValue('createSiteObject', new SiteObjectDeleteCommandTestVersion2());

    $object_data = array('class_name' => 'some_class');

    $this->datasource->expectOnce('setRequest', array(new IsAExpectation('MockRequest')));
    $this->datasource->expectOnce('fetch');
    $this->datasource->setReturnValue('fetch', $object_data);

    try
    {
      $this->assertEqual($this->delete_command->perform(), LIMB_STATUS_ERROR);
      $this->assertTrue(false);
    }
    catch(SQLException $e)
    {
      $this->assertEqual($e->getMessage(), 'catch me!');
    }
  }

}

?>