<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: materialized_path_driver_test.class.php 916 2004-11-23 09:14:28Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/tree/TreeDecorator.class.php');
require_once(dirname(__FILE__) . '/MaterializedPathTreeTest.class.php');

class MaterializedPathTreeDecoratorTest extends MaterializedPathTreeTest
{
  function MaterializedPathTreeDecoratorTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function _createTreeImp()
  {
    return new TreeDecorator(new MaterializedPathTreeTestVersion());
  }
}
?>