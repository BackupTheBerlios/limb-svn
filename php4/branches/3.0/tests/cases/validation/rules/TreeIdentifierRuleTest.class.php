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
require_once(WACT_ROOT . '/iterator/arraydataset.inc.php');
require_once(LIMB_DIR . '/core/validators/rules/TreeIdentifierRule.class.php');
require_once(LIMB_DIR . '/core/tree/Tree.interface.php');

Mock :: generate('LimbBaseToolkit');
Mock :: generate('Tree');

class TreeIdentifierRuleTest extends SingleFieldRuleTestCase
{
  var $tree;
  var $toolkit;

  function TreeIdentifierRuleTest()
  {
    parent :: SingleFieldRuleTestCase('tree identifier rule test');
  }

  function setUp()
  {
    parent :: setUp();

    $this->tree = & new MockTree($this);

    $this->toolkit =& new MockLimbBaseToolkit($this);
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

  function testValidNoSuchParentNode()
  {
    $this->tree->expectOnce('isNode', array($parent_node_id  = 10));
    $this->tree->setReturnValue('isNode', false);

    $this->validator->addRule(new TreeIdentifierRule('test', $parent_node_id));

    $data = new Dataspace();
    $data->set('test', 'test');

    $this->ErrorList->expectNever('addError');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->isValid());
  }

  function testValidNoChildren()
  {
    $this->tree->expectOnce('isNode', array($parent_node_id  = 10));
    $this->tree->setReturnValue('isNode', true);

    $this->tree->expectOnce('getChildren', array($parent_node_id  = 10));

    $rs = new ArrayDataSet(array());
    $this->tree->setReturnReference('getChildren', $rs);

    $this->validator->addRule(new TreeIdentifierRule('test', $parent_node_id));

    $data = new Dataspace();
    $data->set('test', 'test');

    $this->ErrorList->expectNever('addError');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->isValid());
  }

  function testNotValid()
  {
    $this->tree->expectOnce('isNode', array($parent_node_id  = 10));
    $this->tree->setReturnValue('isNode', true);

    $this->tree->expectOnce('getChildren', array($parent_node_id  = 10));

    $rs = new ArrayDataSet(array(array('identifier' => $identifier = 'test')));
    $this->tree->setReturnReference('getChildren', $rs);

    $this->validator->addRule(new TreeIdentifierRule('test', $parent_node_id));

    $data = new Dataspace();
    $data->set('test', $identifier);

    $this->ErrorList->expectOnce('addError');

    $this->validator->validate($data);
    $this->assertFalse($this->validator->isValid());
  }

  function testValidSinceTheSameObject()
  {
    $this->tree->expectOnce('isNode', array($parent_node_id  = 10));
    $this->tree->setReturnValue('isNode', true);

    $this->tree->expectOnce('getChildren', array($parent_node_id  = 10));

    $rs = new ArrayDataSet(array(array('identifier' => $identifier = 'test',
                                       'id' => $node_id = 100)));
    $this->tree->setReturnReference('getChildren', $rs);

    $this->validator->addRule(new TreeIdentifierRule('test', $parent_node_id, $node_id));

    $data = new Dataspace();
    $data->set('test', $identifier);

    $this->ErrorList->expectNever('addError');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->isValid());
  }
}

?>