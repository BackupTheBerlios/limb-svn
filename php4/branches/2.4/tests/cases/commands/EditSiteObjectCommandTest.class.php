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
require_once(LIMB_DIR . '/core/commands/EditSiteObjectCommand.class.php');
require_once(LIMB_DIR . '/core/request/Request.class.php');
require_once(LIMB_DIR . '/core/LimbToolkit.interface.php');
require_once(LIMB_DIR . '/core/site_objects/SiteObject.class.php');
require_once(LIMB_DIR . '/core/daos/RequestedObjectDAO.class.php');

Mock :: generate('LimbToolkit');
Mock :: generate('Request');
Mock :: generate('RequestedObjectDAO');
Mock :: generate('Dataspace');
Mock :: generate('SiteObject');

//do you miss namespaces? yeah, we too :)
class SiteObjectForEditSiteObjectCommand extends SiteObject
{
  function update($force_create_new_version = true)
  {
    return throw(new LimbException('catch me!'));
  }
}

class EditSiteObjectCommandTest extends LimbTestCase
{
  var $command;
  var $request;
  var $toolkit;
  var $dao;
  var $dataspace;
  var $site_object;

  function EditSiteObjectCommandTest()
  {
    parent :: LimbTestCase('edit site object cmd test');
  }

  function setUp()
  {
    $this->request = new MockRequest($this);
    $this->dao = new MockRequestedObjectDAO($this);
    $this->dataspace = new MockDataspace($this);
    $this->site_object = new MockSiteObject($this);

    $this->toolkit = new MockLimbToolkit($this);
    $this->toolkit->setReturnReference('createDAO', $this->dao, array('RequestedObjectDAO'));
    $this->toolkit->setReturnReference('getRequest', $this->request);
    $this->toolkit->setReturnReference('getDataspace', $this->dataspace);

    $this->toolkit->expectOnce('createSiteObject', array('siteObject'));

    Limb :: registerToolkit($this->toolkit);

    $this->command = new EditSiteObjectCommand();
  }

  function tearDown()
  {
    Limb :: popToolkit();

    $this->request->tally();
    $this->dao->tally();
    $this->toolkit->tally();
    $this->dataspace->tally();
    $this->site_object->tally();
  }

  function testPerformFailure()
  {
    $this->toolkit->setReturnReference('createSiteObject',
                                   new SiteObjectForEditSiteObjectCommand(),
                                   array('site_object'));

    $this->assertEqual(LIMB_STATUS_ERROR, $this->command->perform());
  }

  function testPerformOkNoVersionIncrease()
  {
    $this->dataspace->expectOnce('export');
    $this->dataspace->setReturnValue('export', $data = array('identifier' => 'test',
                                                     'title' => 'test',
                                                     ));

    $this->dao->expectOnce('setRequest', array(new IsAExpectation('MockRequest')));
    $this->dao->expectOnce('fetch');
    $this->dao->setReturnValue('fetch',
                                   array('nodeId' => 100,
                                         'someOtherAttrib' => 'someValue'));

    $this->site_object->expectOnce('merge', array($data));

    $this->site_object->expectOnce('update', array(false));

    $this->toolkit->setReturnReference('createSiteObject', $this->site_object, array('site_object'));

    $this->assertEqual(LIMB_STATUS_OK, $this->command->perform());
  }
}

?>