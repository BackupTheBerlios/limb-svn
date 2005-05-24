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
require_once(LIMB_DIR . '/core/etc/limb_util.inc.php');

class JIPProcessor
{
  function process(&$object)
  {
    if (!$actions = $object->get('actions'))
      return;

    $path = $object->get('_node_path');
    $jip_actions = array();
    foreach($actions as $key => $action)
    {
      if(!isset($action['jip']) || !$action['jip'])
        continue;

      $items = array('action' => $key);

      if(isset($action['popup']) && $action['popup'])
        $items['popup'] = 1;

      $jip_href = addUrlQueryItems($path, $items);
      $jip_actions[$key] = $action;
      $jip_actions[$key]['jip_href'] = $jip_href;
      $jip_actions[$key]['name'] = $key;
    }

    $object->set('jip_actions', $jip_actions);
  }
}

?>
