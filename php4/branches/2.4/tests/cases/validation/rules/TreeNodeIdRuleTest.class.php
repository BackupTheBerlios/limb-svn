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
require_once(dirname(__FILE__) . '/SingleFieldRuleTest.class.php');
require_once(LIMB_DIR . '/class/lib/db/DbFactory.class.php');
require_once(LIMB_DIR . '/class/core/Dataspace.class.php');
require_once(LIMB_DIR . '/class/validators/rules/TreeNodeIdRule.class.php');

class TreeNodeIdRuleTest extends SingleFieldRuleTest
{
  var $db = null;
  var $node_id_root;
  var $node_id_document;

  function setUp()
  {
    parent :: setUp();

    $this->db =& DbFactory :: instance();

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
    $this->db->sqlDelete('sys_site_object_tree');
  }

  function testTreeNodeIdRuleBlank()
  {
    $this->validator->addRule(new TreeNodeIdRule('test'));

    $data = new Dataspace();
    $data->set('test', '');

    $this->error_list->expectNever('addError');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->isValid());
  }

  function testTreeNodeIdRuleFalse()
  {
    $this->validator->addRule(new TreeNodeIdRule('test'));

    $data = new Dataspace();
    $data->set('test', false);

    $this->error_list->expectOnce('addError', array('test', Strings :: get('error_invalid_tree_node_id', 'error'), array()));

    $this->validator->validate($data);
    $this->assertFalse($this->validator->isValid());
  }

  function testTreeNodeIdRuleNormal()
  {
    $this->validator->addRule(new TreeNodeIdRule('test'));

    $data = new Dataspace();
    $data->set('test', $this->node_id_document);

    $this->error_list->expectNever('addError');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->isValid());
  }

  function testTreeNodeIdRuleError()
  {
    $this->validator->addRule(new TreeNodeIdRule('test'));

    $data = new Dataspace();
    $data->set('test', -10000);

    $this->error_list->expectOnce('addError', array('test', Strings :: get('error_invalid_tree_node_id', 'error'), array()));

    $this->validator->validate($data);
    $this->assertFalse($this->validator->isValid());
  }
}

?>