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
require_once(LIMB_DIR . '/core/DAO/MappedObjectDAO.class.php');

class MappedObjectDAOTest extends LimbTestCase
{
  function MappedObjectDAOTest()
  {
    parent :: LimbTestCase('mapped object dao tests');
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

    $object = new Object();
    $object->set('class_name', $class_name = 'TestArticle');

    $toolkit->setCurrentEntity($object);

    $dao = new MappedObjectDAO();
    $this->assertEqual($dao->fetch(), $object);
  }
}
?>
