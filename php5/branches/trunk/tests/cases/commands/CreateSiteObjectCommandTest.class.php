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
require_once(LIMB_DIR . '/class/core/commands/CreateSiteObjectCommand.class.php');
require_once(LIMB_DIR . '/class/core/request/Request.class.php');
require_once(LIMB_DIR . '/class/core/datasources/RequestedObjectDatasource.class.php');
require_once(LIMB_DIR . '/class/core/LimbToolkit.interface.php');
require_once(LIMB_DIR . '/class/core/site_objects/SiteObject.class.php');
require_once(LIMB_DIR . '/class/core/behaviours/SiteObjectBehaviour.class.php');

Mock :: generate('LimbToolkit');
Mock :: generate('Request');
Mock :: generate('RequestedObjectDatasource');
Mock :: generate('Dataspace');
Mock :: generate('SiteObject');
Mock :: generate('SiteObjectBehaviour');

//do you miss namespaces? yeah, we too :)
class SiteObjectForCreateSiteObjectCommand extends SiteObject
{
  public function create($is_root = false)
  {
    throw new LimbException('catch me!');
  }
}

class CreateSiteObjectCommandTest extends LimbTestCase
{
  var $command;
  var $request;
  var $toolkit;
  var $datasource;
  var $dataspace;
  var $site_object;
  var $behaviour;

  function setUp()
  {
    $this->request = new MockRequest($this);
    $this->datasource = new MockRequestedObjectDatasource($this);
    $this->dataspace = new MockDataspace($this);
    $this->site_object = new MockSiteObject($this);
    $this->behaviour = new MockSiteObjectBehaviour($this);

    $this->toolkit = new MockLimbToolkit($this);
    $this->toolkit->setReturnValue('getDatasource', $this->datasource, array('requested_object_datasource'));
    $this->toolkit->setReturnValue('getRequest', $this->request);
    $this->toolkit->setReturnValue('getDataspace', $this->dataspace);
    $this->toolkit->setReturnValue('createBehaviour',
                                   $this->behaviour,
                                   array($behaviour_name = 'some_behaviour'));

    $this->toolkit->expectOnce('createSiteObject', array('siteObject'));

    Limb :: registerToolkit($this->toolkit);

    $this->command = new CreateSiteObjectCommand($behaviour_name);
  }

  function tearDown()
  {
    Limb :: popToolkit();

    $this->request->tally();
    $this->datasource->tally();
    $this->toolkit->tally();
    $this->dataspace->tally();
    $this->site_object->tally();
  }

  function testPerformOkNoParentId()
  {
    $this->dataspace->expectOnce('export');
    $this->dataspace->setReturnValue('export', $data = array('identifier' => 'test',
                                                             'title' => 'test'));

    $this->dataspace->expectOnce('get', array('parentNodeId'));
    $this->dataspace->setReturnValue('get', null, array('parent_node_id'));

    $this->datasource->expectOnce('fetch');
    $this->datasource->expectOnce('setRequest', array(new IsAExpectation('MockRequest')));
    $this->datasource->setReturnValue('fetch', array('nodeId' => 100));

    $this->site_object->expectOnce('merge', array($data));

    $this->behaviour->expectOnce('getId');
    $this->behaviour->setReturnValue('getId', $behaviour_id = 100);

    $this->site_object->expectOnce('setBehaviourId', array($behaviour_id));
    $this->site_object->expectOnce('set', array('parentNodeId', 100));

    $this->site_object->expectOnce('create');

    $this->toolkit->setReturnValue('createSiteObject', $this->site_object, array('site_object'));

    $this->dataspace->expectOnce('set', array('createdSiteObject', new IsAExpectation('MockSiteObject')));

    $this->assertEqual(Limb :: STATUS_OK, $this->command->perform());
  }

  function testPerformOkParentId()
  {
    $this->dataspace->expectOnce('export');
    $this->dataspace->setReturnValue('export', $data = array('identifier' => 'test',
                                                     'title' => 'test',
                                                     'parentNodeId' => 200
                                                     ));

    $this->dataspace->expectOnce('get', array('parentNodeId'));
    $this->dataspace->setReturnValue('get', 200, array('parentNodeId'));

    $this->datasource->expectNever('fetch');

    $this->site_object->expectOnce('merge', array($data));

    $this->behaviour->expectOnce('getId');
    $this->behaviour->setReturnValue('getId', $behaviour_id = 100);

    $this->site_object->expectOnce('setBehaviourId', array($behaviour_id));

    $this->site_object->expectOnce('create');

    $this->toolkit->setReturnValue('createSiteObject', $this->site_object, array('site_object'));

    $this->dataspace->expectOnce('set', array('createdSiteObject', new IsAExpectation('MockSiteObject')));

    $this->assertEqual(Limb :: STATUS_OK, $this->command->perform());
  }

  function testPerformFailed()
  {
    $this->dataspace->expectOnce('export');
    $this->dataspace->setReturnValue('export', array('identifier' => 'test',
                                                     'title' => 'test',
                                                     'parentNodeId' => 200
                                                     ));

    $this->dataspace->expectOnce('get', array('parentNodeId'));
    $this->dataspace->setReturnValue('get', 200, array('parentNodeId'));

    $this->datasource->expectNever('fetch');

    $this->toolkit->setReturnValue('createSiteObject',
                                   new SiteObjectForCreateSiteObjectCommand(),
                                   array('site_object'));

    $this->dataspace->expectNever('set', array('createdSiteObject',
                                               new IsAExpectation('MockSiteObject')));

    $this->assertEqual(Limb :: STATUS_ERROR, $this->command->perform());
  }

}

?>