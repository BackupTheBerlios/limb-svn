<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/

interface authorizer
{
  public function get_accessible_object_ids($ids, $action = 'display', $class_id = null);

  public function assign_actions_to_objects(&$objects_data);
}
?>