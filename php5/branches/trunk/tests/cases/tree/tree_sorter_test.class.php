<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/

require_once(LIMB_DIR . '/class/core/tree/tree_sorter.class.php');

class tree_sorter_test extends LimbTestCase
{
  function tree_sorter_test() 
  {
  	parent :: LimbTestCase();
  }
    
  function test_sort()
  {
  	$raw_tree_array = array(
  		array('id' => 1, 'parent_id' => 0, 'sort1' => 'bill', 'sort2' => 0),
  			array('id' => 2, 'parent_id' => 1, 'sort1' => 'body', 'sort2' => 1),
  				array('id' => 3, 'parent_id' => 2, 'sort1' => 'merfy', 'sort2' => 0),
  				array('id' => 4, 'parent_id' => 2, 'sort1' => 'eddy', 'sort2' => 1),
  			array('id' => 5, 'parent_id' => 1, 'sort1' => 'body', 'sort2' => 0),
  		array('id' => 6, 'parent_id' => 0, 'sort1' => 'alfred', 'sort2' => 1),
  			array('id' => 7, 'parent_id' => 6, 'sort1' => 'tom', 'sort2' => 0),
  		array('id' => 8, 'parent_id' => 0, 'sort1' => 'cunny', 'sort2' => 4),
  	);

  	$expected_tree_array = array(
  		array('id' => 8, 'parent_id' => 0, 'sort1' => 'cunny', 'sort2' => 4),
  		array('id' => 1, 'parent_id' => 0, 'sort1' => 'bill', 'sort2' => 0),
				array('id' => 5, 'parent_id' => 1, 'sort1' => 'body', 'sort2' => 0),  		
  			array('id' => 2, 'parent_id' => 1, 'sort1' => 'body', 'sort2' => 1),
  				array('id' => 3, 'parent_id' => 2, 'sort1' => 'merfy', 'sort2' => 0),
  				array('id' => 4, 'parent_id' => 2, 'sort1' => 'eddy', 'sort2' => 1),  		
  		array('id' => 6, 'parent_id' => 0, 'sort1' => 'alfred', 'sort2' => 1),
  			array('id' => 7, 'parent_id' => 6, 'sort1' => 'tom', 'sort2' => 0),
  	);
  	
  	$sorted = tree_sorter :: sort($raw_tree_array, array('sort1' => 'DESC', 'sort2' => 'ASC'));
  	
  	$this->assertEqual(
  		$sorted,
  		$expected_tree_array
  	);
  }
}

?>