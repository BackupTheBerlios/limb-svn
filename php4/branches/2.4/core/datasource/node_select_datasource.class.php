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
require_once(LIMB_DIR . '/core/fetcher.class.php');
require_once(LIMB_DIR . '/core/datasource/fetch_sub_branch_datasource.class.php');

class node_select_datasource extends fetch_sub_branch_datasource
{
  function & get_dataset(&$counter, $params = array())
  {
    $params['depth'] = 1;

    $request = request :: instance();

    if($request->get_attribute('only_parents') == 'false')
      $params['only_parents'] = false;
    else
      $params['only_parents'] = true;

    $params['restrict_by_class'] = false;
    $params['path'] = $this->_process_path();

    return parent :: get_dataset($counter, $params);
  }

  function _process_path()
  {
    $default_path = '/root/';

    $request = request :: instance();

    if(!$path = $request->get_attribute('path'))
      return $default_path;

    if(strpos($path, '?') !== false)
    {
      if(!$node = map_url_to_node($path))
        return $default_path;

      $tree =& tree :: instance();
      if(!$path = $tree->get_path_to_node($node))
        return $default_path;
    }
    return $path;
  }

}



?>