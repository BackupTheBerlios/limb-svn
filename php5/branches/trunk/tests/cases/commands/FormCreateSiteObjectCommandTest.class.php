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
require_once(LIMB_DIR . '/class/core/commands/FormCreateSiteObjectCommand.class.php');
require_once(LIMB_DIR . '/class/core/request/Request.class.php');
require_once(LIMB_DIR . '/class/core/LimbToolkit.interface.php');
require_once(LIMB_DIR . '/class/validators/Validator.class.php');
require_once(LIMB_DIR . '/class/core/datasources/RequestedObjectDatasource.class.php');

Mock :: generate('LimbToolkit');
Mock :: generate('Request');
Mock :: generate('RequestedObjectDatasource');
Mock :: generate('Validator');
Mock :: generate('Dataspace');

Mock :: generatePartial(
                      'FormCreateSiteObjectCommand',
                      'FormCreateSiteObjectCommandTestVersion',
                      array('_isFirstTime', '_getValidator')
                      );

class FormCreateSiteObjectCommandTest extends LimbTestCase
{
  var $command;
  var $request;
  var $toolkit;
  var $datasource;
  var $validator;
  var $dataspace;

  function setUp()
  {
    $this->request = new MockRequest($this);
    $this->datasource = new MockRequestedObjectDatasource($this);
    $this->validator = new MockValidator($this);
    $this->dataspace = new MockDataspace($this);

    $this->toolkit = new MockLimbToolkit($this);
    $this->toolkit->setReturnValue('getDatasource', $this->datasource, array('RequestedObjectDatasource'));
    $this->toolkit->setReturnValue('getRequest', $this->request);
    $this->toolkit->setReturnValue('switchDataspace', $this->dataspace, array('test_form'));

    Limb :: registerToolkit($this->toolkit);

    $this->command = new FormCreateSiteObjectCommandTestVersion($this);
    $this->command->__construct('test_form');

    $this->command->setReturnValue('_isFirstTime', false);
    $this->command->setReturnValue('_getValidator', $this->validator);
    $this->validator->setReturnValue('validate', true);
  }

  function tearDown()
  {
    Limb :: popToolkit();

    $this->request->tally();
    $this->datasource->tally();
    $this->toolkit->tally();
    $this->validator->tally();
    $this->dataspace->tally();
  }

  function testRegisterValidationRulesNoParentNodeId()
  {
    $object_data = array('parent_node_id' => 100);

    $this->dataspace->expectOnce('get', array('parentNodeId'));
    $this->dataspace->setReturnValue('get', null);

    $this->datasource->expectOnce('setRequest', array(new IsAExpectation('MockRequest')));
    $this->datasource->expectOnce('fetch');
    $this->datasource->setReturnValue('fetch', $object_data);

    $this->validator->expectCallCount('addRule', 2);
    $this->validator->expectArgumentsAt(0, 'addRule', array(array(LIMB_DIR . '/class/validators/rules/treeNodeIdRule', 'parentNodeId')));
    $this->validator->expectArgumentsAt(1, 'addRule', array(array(LIMB_DIR . '/class/validators/rules/treeIdentifierRule', 'identifier', 100)));

    $this->command->perform();
  }

  function testRegisterValidationRules()
  {
    $this->dataspace->expectOnce('get', array('parentNodeId'));
    $this->dataspace->setReturnValue('get', 100);

    $this->datasource->expectNever('fetch');

    $this->validator->expectCallCount('addRule', 2);
    $this->validator->expectArgumentsAt(0, 'addRule', array(array(LIMB_DIR . '/class/validators/rules/treeNodeIdRule', 'parentNodeId')));
    $this->validator->expectArgumentsAt(1, 'addRule', array(array(LIMB_DIR . '/class/validators/rules/treeIdentifierRule', 'identifier', 100)));

    $this->command->perform();
  }

}

?>