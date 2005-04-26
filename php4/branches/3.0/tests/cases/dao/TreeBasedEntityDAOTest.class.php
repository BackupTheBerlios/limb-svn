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
require_once(LIMB_DIR . '/core/dao/TreeBasedEntityDAO.class.php');
require_once(LIMB_DIR . '/core/request_resolvers/RequestResolver.interface.php');

Mock :: generate('RequestResolver');

class TreeBasedEntityDAOTest extends LimbTestCase
{
  function TreeBasedEntityDAOTest()
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
    $resolver = new MockRequestResolver($this);
    $toolkit->setRequestResolver('tree_based_entity', $resolver);

    $resolver->tally();

    $entity = new Object();
    $entity->set('class_name', $class_name = 'TestArticle');

    $request =& $toolkit->getRequest();
    $resolver->setReturnReference('resolve', $entity, array($request));

    $dao = new TreeBasedEntityDAO();
    $result =& $dao->fetch();
    $expected_result = new Dataspace();
    $expected_result->set('class_name', $class_name);

    $this->assertEqual($result, $expected_result);
  }
}
?>
