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
require_once(LIMB_DIR . '/core/datasources/FetchTreeDatasource.class.php');
require_once(LIMB_DIR . '/core/http/Uri.class.php');

class TreeNavigationDatasource extends FetchTreeDatasource
{
  function _fetch(&$counter, $params)
  {
    $result = parent :: _fetch($counter, $params);
    $uri = new Uri($_SERVER['PHP_SELF']);

    foreach($result as $key => $data)
    {
      if(is_integer($res = $uri->comparePath(new Uri($data['url']))))
      {
        if($res >= 0)
          $result[$key]['in_path'] = true;
        if($res == 0)
          $result[$key]['selected'] = true;
      }
    }

    return $result;
  }
}


?>