<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/datasource/fetch_sub_branch_datasource.class.php');

define ('DEFAULT_BRANCH_RANDOM_LIMIT', 3);

class random_branch_objects_datasource extends fetch_sub_branch_datasource
{
  function & _fetch(&$counter, $params = array())
  {
    $limit = DEFAULT_BRANCH_RANDOM_LIMIT;

    if (isset($params['limit']))
    {
      $limit = $params['limit'];
      unset($params['limit']);
    }

    if(!$all_objects =& parent :: _fetch($counter, $params))
      return array();

    $result = array();

    if ($limit >= count($all_objects))
      $limit = count($all_objects);

    $max_index = count($all_objects) - 1;
    $indexes = array_keys($all_objects);

    while(count($result) < $limit)
    {
      $index = mt_rand(0, $max_index);
      if (!isset($result[$index]))
        $result[$index] = $all_objects[$indexes[$index]];
    }

    return $result;
  }
}


?>