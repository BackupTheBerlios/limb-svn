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
  function process(& $object)
  {
    if (!$actions = $object->get('actions'))
      return $object;

    $path = $object->get('path');
    foreach($actions as $key => $action)
    {
      if(!isset($action['jip']) || !$action['jip'])
        continue;

      $items = array('action' => $key);
      $jip_href = addUrlQueryItems($path, $items);
      $actions[$key]['jip_href'] = $jip_href;
      $actions[$key]['name'] = $key;
    }

    $object->set('actions', $actions);
  }
}

?>
