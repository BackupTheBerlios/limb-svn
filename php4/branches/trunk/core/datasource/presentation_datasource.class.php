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

class presentation_datasource extends fetch_sub_branch_datasource
{
  function & _fetch(&$counter, $params)
  {
    $items =& parent :: _fetch($counter, $params);

    if (!count($items))
      return array();

    reset($items);

    $request = request :: instance();

    if (!$current_id = $request->get_attribute('id'))
      $current_id = $items[key($items)]['id'];

    foreach($items as $id => $item)
    {
      if ($current_id == $item['id'])
      {
        $current_item = $item;
        break;
      }
      else
        next($items);
    }

    $result = array();
    $result['prev'] = array($this->_get_prev_item($items, $current_id));
    $result['next'] = array($this->_get_next_item($items, $current_id));
    $result['current'] = array($current_item);

    $result['prev']['presentation_path'] = $params['path'];
    $result['next']['presentation_path'] = $params['path'];
    $result['current']['presentation_path'] = $params['path'];

    return array($result);
  }

  function _get_next_item(&$items, $current_id)
  {
    reset($items);
    foreach(array_keys($items) as $id)
      if ($current_id == $items[$id]['id'])
      {
        break;
      }
      else
      {
        next($items);
      }

    if (($item = next($items)) !== false)
      return $item;
    else
      return array();
  }

  function _get_prev_item(&$items, $current_id)
  {
    reset($items);
    $prev = array();

    foreach(array_keys($items) as $id)
      if ($current_id == $items[$id]['id'])
      {
        break;
      }
      else
      {
        next($items);
      }

    if (($item = prev($items)) !== false)
      return $item;
    else
      return array();

    if (!$prev)
      return array();
    if($prev['id'] == $current_id)
      return array();
    return $prev;
  }
}


?>