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
require_once(LIMB_DIR . '/core/datasource/tree_navigation_datasource.class.php');

class pictured_tree_navigation_datasource extends tree_navigation_datasource
{
  function & _fetch(&$counter, $params)
  {
    $tree =& parent :: _fetch($counter, $params);
    
    foreach($tree as $key => $data)
    {
      if($data['level'] == 1)
      {
        $result[$key] = $tree[$key];
        $current_key = $key;
      }
      if($data['level'] > 1)
      {
        $result[$current_key]['child_items'][$key] = $tree[$key];
      }
    }
    return $result;
  }

}


?>
