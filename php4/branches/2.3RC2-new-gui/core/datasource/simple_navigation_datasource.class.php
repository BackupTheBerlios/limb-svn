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

class simple_navigation_datasource extends fetch_sub_branch_datasource
{
  function & _fetch(&$counter, $params)
  {
    $result =& parent :: _fetch($counter, $params);

    $uri = new uri($_SERVER['PHP_SELF']);

    //we're trimming trailing slashes: thus /root/about == /root/about/

    $uri->set_path(rtrim($uri->get_path(), '/'));

    foreach($result as $key => $data)
    {
      $nav_uri = new uri($data['url']);
      $nav_uri->set_path(rtrim($nav_uri->get_path(), '/'));

      if ($uri->get_host() != $nav_uri->get_host())
        continue;

      if(is_integer($res = $uri->compare_path($nav_uri)))
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