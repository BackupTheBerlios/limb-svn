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

interface Authorizer
{
  public function getAccessibleObjectIds($ids, $action = 'display');

  public function assignActionsToObjects(&$objects_data);
}
?>