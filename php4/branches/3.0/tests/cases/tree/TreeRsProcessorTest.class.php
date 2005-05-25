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
require_once(LIMB_DIR . '/core/tree/TreeRsProcessor.class.php');
require_once(WACT_ROOT . '/iterator/arraydataset.inc.php');

class TreeRsProcessorTest extends LimbTestCase
{
  function TreeRsProcessorTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function testMakeNestedOneElementArray()
  {
    $raw_tree_array = array(
      array('id' => 1, 'parent_id' => 0),
      );

    $expected_tree_array = array(
      array('id' => 1, 'parent_id' => 0),
      );

    $nested = TreeRsProcessor :: makeNested($raw_tree_array);

    $this->assertEqual(
      $nested,
      new ArrayDataSet($expected_tree_array)
    );
  }

  function testMakeNestedSimpleArray()
  {
    $raw_tree_array = array(
      array('id' => 1, 'parent_id' => 0),
        array('id' => 2, 'parent_id' => 1),
          array('id' => 5, 'parent_id' => 2),
        array('id' => 3, 'parent_id' => 1),
      array('id' => 4, 'parent_id' => 0),
      );

    $expected_tree_array = array(
      array('id' => 1, 'parent_id' => 0, 'children' =>
            array(
                  array('id' => 2, 'parent_id' => 1, 'children' => array(
                      array('id' => 5, 'parent_id' => 2),
                      )
                  ),
                  array('id' => 3, 'parent_id' => 1),
                  ),
            ),
      array('id' => 4, 'parent_id' => 0)
      );

    $nested = TreeRsProcessor :: makeNested($raw_tree_array);

    $this->assertEqual(
      $nested,
      new ArrayDataSet($expected_tree_array)
    );
  }

  function testMakeNestedMoreComplex()
  {
    $raw_tree_array = array(
      array('id' => 1, 'parent_id' => 0),
        array('id' => 2, 'parent_id' => 1),
          array('id' => 3, 'parent_id' => 2),
          array('id' => 4, 'parent_id' => 2),
        array('id' => 5, 'parent_id' => 1),
      array('id' => 6, 'parent_id' => 0),
        array('id' => 7, 'parent_id' => 6),
      array('id' => 8, 'parent_id' => 0),
    );

    $expected_tree_array = array(
      array('id' => 1, 'parent_id' => 0, 'children' =>
        array(
          array('id' => 2, 'parent_id' => 1, 'children' =>
            array(
              array('id' => 3, 'parent_id' => 2),
              array('id' => 4, 'parent_id' => 2),
              )
            ),
          array('id' => 5, 'parent_id' => 1)
        )
      ),
      array('id' => 6, 'parent_id' => 0, 'children' =>
        array(
              array('id' => 7, 'parent_id' => 6),
        )
      ),
      array('id' => 8, 'parent_id' => 0),
    );

    $nested = TreeRsProcessor :: makeNested($raw_tree_array);

    $this->assertEqual(
      $nested,
      new ArrayDataSet($expected_tree_array)
    );
  }

  function testSortComplex()
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

    $sorted = TreeRsProcessor :: sort($raw_tree_array, array('sort1' => 'DESC', 'sort2' => 'ASC'));

    $this->assertEqual(
      $sorted,
      new ArrayDataSet($expected_tree_array)
    );
  }
}

?>