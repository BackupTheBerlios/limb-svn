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
require_once(LIMB_DIR . '/core/commands/FormCreateSiteObjectCommand.class.php');
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
                      'FormCreateSiteObjectCommand',
                      'FormCreateSiteObjectCommandTestVersion',
                      array('_isFirstTime', '_getValidator')
                      );

class FormCreateSiteObjectCommandTest extends LimbTestCase
{
  var $command;
  var $request;
  var $toolkit;
  var $dao;
  var $validator;
  var $dataspace;

  function FormCreateSiteObjectCommandTest()
  {
    parent :: LimbTestCase('form create site object cmd test');
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

    $this->command = new FormCreateSiteObjectCommandTestVersion($this);
    $this->command->FormCreateSiteObjectCommand('test_form');

    $this->command->setReturnValue('_isFirstTime', false);
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

  function testRegisterValidationRulesNoParentNodeId()
  {
    $object_data = array('parent_node_id' => 100);

    $this->dataspace->expectOnce('get', array('parentNodeId'));
    $this->dataspace->setReturnValue('get', null);

    $this->dao->expectOnce('setRequest', array(new IsAExpectation('MockRequest')));
    $this->dao->expectOnce('fetch');
    $this->dao->setReturnValue('fetch', $object_data);

    $this->validator->expectCallCount('addRule', 2);
    $this->validator->expectArgumentsAt(0, 'addRule', array(array(LIMB_DIR . '/core/validators/rules/treeNodeIdRule', 'parentNodeId')));
    $this->validator->expectArgumentsAt(1, 'addRule', array(array(LIMB_DIR . '/core/validators/rules/treeIdentifierRule', 'identifier', 100)));

    $this->command->perform();
  }

  function testRegisterValidationRules()
  {
    $this->dataspace->expectOnce('get', array('parentNodeId'));
    $this->dataspace->setReturnValue('get', 100);

    $this->dao->expectNever('fetch');

    $this->validator->expectCallCount('addRule', 2);
    $this->validator->expectArgumentsAt(0, 'addRule', array(array(LIMB_DIR . '/core/validators/rules/treeNodeIdRule', 'parentNodeId')));
    $this->validator->expectArgumentsAt(1, 'addRule', array(array(LIMB_DIR . '/core/validators/rules/treeIdentifierRule', 'identifier', 100)));

    $this->command->perform();
  }

}

?>