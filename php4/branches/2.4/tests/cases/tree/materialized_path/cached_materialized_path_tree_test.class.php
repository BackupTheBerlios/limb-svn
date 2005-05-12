<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: materialized_path_imp_test.class.php 916 2004-11-23 09:14:28Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/tree/caching_tree.class.php');
require_once(dirname(__FILE__) . '/materialized_path_tree_test.class.php');
//require_once(LIMB_DIR . '/core/cache/cache_registry.class.php');

class cached_materialized_path_tree_test extends materialized_path_tree_test
{
  function _create_tree_imp()
  {
    return new caching_tree(new materialized_path_tree_test_version());
  }

  function setUp()
  {
    parent :: setUp();

    $this->imp->flush_cache();
  }

  function tearDown()
  {
    parent :: tearDown();

    $this->imp->flush_cache();
  }
}
?>