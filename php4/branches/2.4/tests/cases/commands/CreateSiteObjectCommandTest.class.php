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
require_once(LIMB_DIR . '/core/commands/CreateSiteObjectCommand.class.php');
require_once(WACT_ROOT . '/datasource/dataspace.inc.php');
require_once(LIMB_DIR . '/core/request/Request.class.php');
require_once(LIMB_DIR . '/core/daos/RequestedObjectDAO.class.php');
require_once(LIMB_DIR . '/core/LimbToolkit.interface.php');
require_once(LIMB_DIR . '/core/site_objects/SiteObject.class.php');
require_once(LIMB_DIR . '/core/behaviours/SiteObjectBehaviour.class.php');

Mock :: generate('LimbToolkit');
Mock :: generate('Request');
Mock :: generate('RequestedObjectDAO');
Mock :: generate('Dataspace');
Mock :: generate('SiteObject');
Mock :: generate('SiteObjectBehaviour');

//do you miss namespaces? yeah, we too :)
class SiteObjectForCreateSiteObjectCommand extends SiteObject
{
  function create($is_root = false)
  {
    return throw(new LimbException('catch me!'));
  }
}

class CreateSiteObjectCommandTest extends LimbTestCase
{
  var $command;
  var $request;
  var $toolkit;
  var $dao;
  var $dataspace;
  var $site_object;
  var $behaviour;

  function CreateSiteObjectCommandTest()
  {
    parent :: LimbTestCase('create site object cmd test');
  }

  function setUp()
  {
    $this->request = new MockRequest($this);
    $this->dao = new MockRequestedObjectDAO($this);
    $this->dataspace = new MockDataspace($this);
    $this->site_object = new MockSiteObject($this);
    $this->behaviour = new MockSiteObjectBehaviour($this);

    $this->toolkit = new MockLimbToolkit($this);
    $this->toolkit->setReturnReference('createDAO', $this->dao, array('RequestedObjectDAO'));
    $this->toolkit->setReturnReference('getRequest', $this->request);
    $this->toolkit->setReturnReference('getDataspace', $this->dataspace);
    $this->toolkit->setReturnReference('createBehaviour',
                                   $this->behaviour,
                                   array($behaviour_name = 'some_behaviour'));

    $this->toolkit->expectOnce('createSiteObject', array('SiteObject'));

    Limb :: registerToolkit($this->toolkit);

    $this->command = new CreateSiteObjectCommand($behaviour_name);
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

  function testPerformOkNoParentId()
  {
    $this->dataspace->expectOnce('export');
    $this->dataspace->setReturnValue('export', $data = array('identifier' => 'test',
                                                             'title' => 'test'));

    $this->dataspace->expectOnce('get', array('parent_node_id'));
    $this->dataspace->setReturnValue('get', null, array('parent_node_id'));

    $this->dao->expectOnce('fetch');
    $this->dao->expectOnce('setRequest', array(new IsAExpectation('MockRequest')));
    $this->dao->setReturnValue('fetch', array('node_id' => 100));

    $this->site_object->expectOnce('merge', array($data));

    $this->behaviour->expectOnce('getId');
    $this->behaviour->setReturnValue('getId', $behaviour_id = 100);

    $this->site_object->expectOnce('setBehaviourId', array($behaviour_id));
    $this->site_object->expectOnce('set', array('parent_node_id', 100));

    $this->site_object->expectOnce('create');

    $this->toolkit->setReturnReference('createSiteObject', $this->site_object, array('SiteObject'));

    $this->dataspace->expectOnce('set', array('createdSiteObject', new IsAExpectation('MockSiteObject')));

    $this->assertEqual(LIMB_STATUS_OK, $this->command->perform());
  }

  function testPerformOkParentId()
  {
    $this->dataspace->expectOnce('export');
    $this->dataspace->setReturnValue('export', $data = array('identifier' => 'test',
                                                     'title' => 'test',
                                                     'parent_node_id' => 200
                                                     ));

    $this->dataspace->expectOnce('get', array('parent_node_id'));
    $this->dataspace->setReturnValue('get', 200, array('parent_node_id'));

    $this->dao->expectNever('fetch');

    $this->site_object->expectOnce('merge', array($data));

    $this->behaviour->expectOnce('getId');
    $this->behaviour->setReturnValue('getId', $behaviour_id = 100);

    $this->site_object->expectOnce('setBehaviourId', array($behaviour_id));

    $this->site_object->expectOnce('create');

    $this->toolkit->setReturnReference('createSiteObject', $this->site_object, array('SiteObject'));

    $this->dataspace->expectOnce('set', array('createdSiteObject', new IsAExpectation('MockSiteObject')));

    $this->assertEqual(LIMB_STATUS_OK, $this->command->perform());
  }

  function testPerformFailed()
  {
    $this->dataspace->expectOnce('export');
    $this->dataspace->setReturnValue('export',
                                     array('identifier' => 'test',
                                           'title' => 'test',
                                           'parent_node_id' => 200));

    $this->dataspace->expectOnce('get', array('parent_node_id'));
    $this->dataspace->setReturnValue('get', 200, array('parent_node_id'));

    $this->dao->expectNever('fetch');

    $this->toolkit->setReturnReference('createSiteObject',
                                   new SiteObjectForCreateSiteObjectCommand(),
                                   array('SiteObject'));

    $this->dataspace->expectNever('set', array('createdSiteObject',
                                               new IsAExpectation('MockSiteObject')));

    $this->assertEqual(LIMB_STATUS_ERROR, $this->command->perform());
  }

}

?>