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
require_once(LIMB_DIR . '/core/db/LimbDbPool.class.php');
require_once(WACT_ROOT . '/datasource/dataspace.inc.php');
require_once(LIMB_DIR . '/core/validators/rules/TreeNodeIdRule.class.php');

class TreeNodeIdRuleTest extends SingleFieldRuleTestCase
{
  var $db = null;
  var $node_id_root;
  var $node_id_document;

  function TreeNodeIdRuleTest()
  {
    parent :: SingleFieldRuleTestCase('tree node id rule test');
  }

  function setUp()
  {
    parent :: setUp();

    $this->db =& new SimpleDb(LimbDbPool :: getConnection());

    $toolkit =& Limb :: toolkit();
    $tree =& $toolkit->getTree();

    $values['identifier'] = 'root';
    $this->node_id_root = $tree->createRootNode($values);

    $values['identifier'] = 'document';
    $values['object_id'] = 10;
    $this->node_id_document = $tree->createSubNode($this->node_id_root, $values);
  }

  function tearDown()
  {
    parent :: tearDown();
    $this->_cleanUp();
  }

  function _cleanUp()
  {
    $this->db->delete('sys_site_object_tree');
  }

  function testTreeNodeIdRuleBlank()
  {
    $this->validator->addRule(new TreeNodeIdRule('test'));

    $data = new Dataspace();
    $data->set('test', '');

    $this->ErrorList->expectNever('addError');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->isValid());
  }

  function testTreeNodeIdRuleFalse()
  {
    $this->validator->addRule(new TreeNodeIdRule('test'));

    $data = new Dataspace();
    $data->set('test', false);

    $this->ErrorList->expectOnce('addError', array('validation', 'ERROR_INVALID_TREE_NODE_ID', array('Field' => 'test'), NULL));

    $this->validator->validate($data);
    $this->assertFalse($this->validator->isValid());
  }

  function testTreeNodeIdRuleNormal()
  {
    $this->validator->addRule(new TreeNodeIdRule('test'));

    $data = new Dataspace();
    $data->set('test', $this->node_id_document);

    $this->ErrorList->expectNever('addError');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->isValid());
  }

  function testTreeNodeIdRuleError()
  {
    $this->validator->addRule(new TreeNodeIdRule('test'));

    $data = new Dataspace();
    $data->set('test', -10000);

    $this->ErrorList->expectOnce('addError', array('validation', 'ERROR_INVALID_TREE_NODE_ID', array('Field' => 'test'), NULL));

    $this->validator->validate($data);
    $this->assertFalse($this->validator->isValid());
  }
}

?>