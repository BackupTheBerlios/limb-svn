<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
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
      if(file_exists(PROJECT_DIR. 'design/main/images/menu_icons/'. $data['identifier'] .'.32.gif'))
      {
        $tree[$key]['image_path'] = '/design/main/images/menu_icons/';
        $tree[$key]['image_name'] = $data['identifier'];
      }
      else if(file_exists(SHARED_DIR. 'images/menu_icons/'. $data['identifier'] .'.32.gif'))
      {
        $tree[$key]['image_path'] = SHARED_IMG_URL . 'menu_icons/';
        $tree[$key]['image_name'] = $data['identifier'];
      }
      else
      {
        $tree[$key]['image_path'] = SHARED_IMG_URL .'menu_icons/';
        $tree[$key]['image_name'] = 'default';
      }

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
