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
require_once(LIMB_DIR . '/core/lib/session/session.class.php');
require_once(LIMB_DIR . '/core/lib/http/uri.class.php');

class session_history_manager
{
  function datermine_tab_id()
  {
    if(isset($_COOKIE["active_tab"]))
      $tab_name = $_COOKIE["active_tab"];
    else
      $tab_name = 'tab';

    return $tab_name;
  }

  function save()
  {
    $request =& request :: instance();
    $tab_id = session_history_manager :: datermine_tab_id();

    if(!$history = session :: get('session_history'))
      $history = array();

    if(!isset($history[$tab_id]))
      $history[$tab_id] = array();

    $uri =& $request->get_uri();

    $uri->remove_query_item('rn');
    if($uri->get_query_item('popup'))
      return;

    $object_data = fetch_requested_object();
    if($object_data['class_name'] == 'control_panel')
      return;

    $history_item = array('title' => $object_data['title'], 'href' => $uri->to_string());

    $first = end($history[$tab_id]);

    if($first)
    {
      $latest_uri =& new uri($first['href']);
      if($uri->compare($latest_uri))
        return;
    }

    if(count($history[$tab_id]) >= 10)
    {
      $history[$tab_id] = array_reverse($history[$tab_id]);
      array_pop($history[$tab_id]);
      $history[$tab_id] = array_reverse($history[$tab_id]);
    }

    array_push($history[$tab_id], $history_item );
    session :: set('session_history', $history);
  }

  function fetch()
  {
    $tab_id = session_history_manager :: datermine_tab_id();

    $history = session :: get('session_history');
    if(!isset($history[$tab_id]))
      $history[$tab_id] = array();

    return  array_reverse($history[$tab_id]);
  }

}

?>