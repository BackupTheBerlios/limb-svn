<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: js_navigation_datasource.class.php 7 2004-07-09 08:32:32Z mike $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/datasource/datasource.class.php');
require_once(LIMB_DIR . '/core/tree/tree_sorter.class.php');

class fetch_one_datasource extends datasource
{

  function & get_dataset(&$counter, $params)
  {
    $item = array();

    if (isset($params['path']))
      $item =& fetch_one_by_path($params['path']);

    return new array_dataset(array($item));
  }
}
?>