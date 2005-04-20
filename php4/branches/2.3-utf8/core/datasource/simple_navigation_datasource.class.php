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
    $this->set_navigation_attributes($result);
    return $result;
  }

  function _compare_with_url($url)
  {
    $uri = new uri($_SERVER['PHP_SELF']);
    //we're trimming trailing slashes: thus /root/about == /root/about/
    $uri->set_path(rtrim($uri->get_path(), '/'));

    $nav_uri = new uri($url);
    $nav_uri->set_path(rtrim($nav_uri->get_path(), '/'));

    if ($uri->get_host() != $nav_uri->get_host())
      return false;
    return $uri->compare_path($nav_uri);
  }

  function set_navigation_attributes(&$items, $attribute = 'url')
  {
    foreach($items as $key => $data)
    {
      if(is_integer($res = $this->_compare_with_url($data[$attribute])))
      {
        if($res >= 0)
          $items[$key]['in_path'] = true;

        if($res == 0)
          $items[$key]['selected'] = true;
      }
    }

  }
}
?>