<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: TreeBranchCriteriaTest.class.php 1173 2005-03-17 11:36:43Z seregalimb $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/DAO/CurrentEntityDAO.class.php');
require_once(LIMB_DIR . '/core/entity/Entity.class.php');
require_once(LIMB_DIR . '/core/NodeConnection.class.php');

class CurrentEntityDAOTest extends LimbTestCase
{
  function CurrentEntityDAOTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    Limb :: saveToolkit();
  }

  function tearDown()
  {
    Limb :: restoreToolkit();
  }

  function testFetch()
  {
    $toolkit =& Limb :: toolkit();

    $entity = new Entity();
    $node = new NodeConnection();
    $node->set('id', $id = 10);
    $entity->set('class_name', $class_name = 'TestArticle');
    $entity->registerPart('node', $node);

    $toolkit->setCurrentEntity($entity);

    $dao = new CurrentEntityDAO();
    $result =& $dao->fetch();
    $expected_result = new Dataspace();
    $expected_result->set('class_name', $class_name);
    $expected_result->set('_node_id', $id);

    $this->assertEqual($result, $expected_result);
  }
}
?>
