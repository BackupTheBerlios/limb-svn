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
require_once(LIMB_DIR . '/core/datasources/FetchSubBranchDatasource.class.php');

class SimpleNavigationDatasource extends FetchSubBranchDatasource
{
  function _fetch(&$counter, $params)
  {
    $result = parent :: _fetch($counter, $params);
    $uri = new Uri($_SERVER['PHP_SELF']);

    foreach($result as $key => $data)
    {
      $nav_uri = new Uri($data['url']);

      if ($uri->getHost() != $nav_uri->getHost())
        continue;

      if(is_integer($res = $uri->comparePath($nav_uri)))
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