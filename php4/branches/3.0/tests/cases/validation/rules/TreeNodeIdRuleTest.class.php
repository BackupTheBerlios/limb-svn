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
require_once(WACT_ROOT . '/../tests/cases/validation/rules/singlefield.inc.php');
require_once(WACT_ROOT . '/datasource/dataspace.inc.php');
require_once(LIMB_DIR . '/core/validators/rules/TreeNodeIdRule.class.php');
require_once(LIMB_DIR . '/core/db/SimpleDb.class.php');
require_once(LIMB_DIR . '/core/tree/Tree.interface.php');

Mock :: generate('LimbBaseToolkit');
Mock :: generate('Tree');

class TreeNodeIdRuleTest extends SingleFieldRuleTestCase
{
  var $toolkit;
  var $tree;

  function TreeNodeIdRuleTest()
  {
    parent :: SingleFieldRuleTestCase('tree node id rule test');
  }

  function setUp()
  {
    parent :: setUp();

    $this->tree = new MockTree($this);
    $this->toolkit = new MockLimbBaseToolkit($this);
    $this->toolkit->setReturnReference('getTree', $this->tree);

    Limb :: registerToolkit($this->toolkit);
  }

  function tearDown()
  {
    $this->toolkit->tally();
    $this->tree->tally();

    Limb :: restoreToolkit();

    parent :: tearDown();
  }

  function testValidEmptyValue()
  {
    $this->tree->expectNever('getNode', array());
    $this->validator->addRule(new TreeNodeIdRule('test'));

    $data = new Dataspace();
    $data->set('test', '');

    $this->ErrorList->expectNever('addError');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->isValid());
  }

  function testNotValieFalseValue()
  {
    $this->tree->expectNever('getNode', array());
    $this->validator->addRule(new TreeNodeIdRule('test'));

    $data = new Dataspace();
    $data->set('test', false);

    $this->ErrorList->expectOnce('addError', array('validation', 'INVALID', array('Field' => 'test'), NULL));

    $this->validator->validate($data);
    $this->assertFalse($this->validator->isValid());
  }

  function testValid()
  {
    $this->tree->expectOnce('getNode', array($node_id = 10));
    $this->tree->setReturnValue('getNode', array('whatever'));

    $this->validator->addRule(new TreeNodeIdRule('test'));

    $data = new Dataspace();
    $data->set('test', $node_id);

    $this->ErrorList->expectNever('addError');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->isValid());
  }
}

?>