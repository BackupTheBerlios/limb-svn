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
require_once(LIMB_DIR . '/core/commands/FormEditSiteObjectCommand.class.php');
require_once(LIMB_DIR . '/core/request/Request.class.php');
require_once(LIMB_DIR . '/core/LimbToolkit.interface.php');
require_once(LIMB_DIR . '/core/daos/RequestedObjectDAO.class.php');

require_once(WACT_ROOT . '/validation/validator.inc.php');

Mock :: generate('LimbToolkit');
Mock :: generate('Request');
Mock :: generate('RequestedObjectDAO');
Mock :: generate('Validator');
Mock :: generate('Dataspace');

Mock :: generatePartial(
                      'FormEditSiteObjectCommand',
                      'FormEditSiteObjectCommandTestVersion',
                      array('_isFirstTime', '_getValidator')
                      );

class FormEditSiteObjectCommandTest extends LimbTestCase
{
  var $command;
  var $request;
  var $toolkit;
  var $dao;
  var $validator;
  var $dataspace;

  function FormEditSiteObjectCommandTest()
  {
    parent :: LimbTestCase('form edit site object cmd test');
  }

  function setUp()
  {
    $this->request = new MockRequest($this);
    $this->dao = new MockRequestedObjectDAO($this);
    $this->validator = new MockValidator($this);
    $this->dataspace = new MockDataspace($this);

    $this->toolkit = new MockLimbToolkit($this);
    $this->toolkit->setReturnReference('createDAO', $this->dao, array('RequestedObjectDAO'));
    $this->toolkit->setReturnReference('getRequest', $this->request);
    $this->toolkit->setReturnReference('switchDataspace', $this->dataspace, array('test_form'));

    Limb :: registerToolkit($this->toolkit);

    $this->command = new FormEditSiteObjectCommandTestVersion($this);
    $this->command->FormEditSiteObjectCommand('test_form');

    $this->command->setReturnReference('_getValidator', $this->validator);
    $this->validator->setReturnValue('validate', true);
  }

  function tearDown()
  {
    Limb :: popToolkit();

    $this->request->tally();
    $this->dao->tally();
    $this->toolkit->tally();
    $this->validator->tally();
    $this->dataspace->tally();
  }

  function testInitDataspaceFirstTime()
  {
    $this->command->setReturnValue('_isFirstTime', true);

    $object_data = array('parent_node_id' => 100,
                         'identifier' => 'some_identifier',
                         'title' => 'some_title');

    $this->dao->expectOnce('setRequest', array(new IsAExpectation('MockRequest')));
    $this->dao->expectOnce('fetch');
    $this->dao->setReturnValue('fetch', $object_data);

    $this->dataspace->expectOnce('merge', array($object_data));

    $this->assertEqual(LIMB_STATUS_FORM_DISPLAYED, $this->command->perform());
  }


  function testRegisterValidationRules1()
  {
    $this->command->setReturnValue('_isFirstTime', false);

    $object_data = array('parent_node_id' => 100, 'node_id' => 110);

    $this->dataspace->expectOnce('get', array('parentNodeId'));
    $this->dataspace->setReturnValue('get', null);

    $this->dao->expectOnce('setRequest', array(new IsAExpectation('MockRequest')));
    $this->dao->expectOnce('fetch');
    $this->dao->setReturnValue('fetch', $object_data);

    $this->validator->expectCallCount('addRule', 3);
    $this->validator->expectArgumentsAt(0, 'addRule', array(array(LIMB_DIR . '/core/validators/rules/treeNodeIdRule', 'parentNodeId')));
    $this->validator->expectArgumentsAt(1, 'addRule', array(array(LIMB_DIR . '/core/validators/rules/requiredRule', 'identifier')));
    $this->validator->expectArgumentsAt(2, 'addRule', array(array(LIMB_DIR . '/core/validators/rules/treeIdentifierRule', 'identifier', 100, 110)));

    $this->command->perform();
  }

  function testRegisterValidationRules2()
  {
    $this->command->setReturnValue('_isFirstTime', false);

    $object_data = array('parent_node_id' => 100, 'node_id' => 110);

    $this->dataspace->expectOnce('get', array('parentNodeId'));
    $this->dataspace->setReturnValue('get', 102);

    $this->dao->expectOnce('setRequest', array(new IsAExpectation('MockRequest')));
    $this->dao->expectOnce('fetch');
    $this->dao->setReturnValue('fetch', $object_data);

    $this->validator->expectCallCount('addRule', 3);
    $this->validator->expectArgumentsAt(0, 'addRule', array(array(LIMB_DIR . '/core/validators/rules/treeNodeIdRule', 'parentNodeId')));
    $this->validator->expectArgumentsAt(1, 'addRule', array(array(LIMB_DIR . '/core/validators/rules/requiredRule', 'identifier')));
    $this->validator->expectArgumentsAt(2, 'addRule', array(array(LIMB_DIR . '/core/validators/rules/treeIdentifierRule', 'identifier', 102, 110)));

    $this->command->perform();
  }
}

?>