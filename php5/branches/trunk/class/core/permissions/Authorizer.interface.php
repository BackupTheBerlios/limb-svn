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

interface authorizer
{
  public function get_accessible_object_ids($ids, $action = 'display');

  public function assign_actions_to_objects(&$objects_data);
}
?>