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
require_once(LIMB_DIR . '/core/db/IteratorDbDecorator.class.php');

class SimpleACLActionsRecordSet extends IteratorDbDecorator
{
  var $authorizer;

  function & current()
  {
    $record =& parent :: current();

    $authorizer =& $this->getAuthorizer();

    $path = $record->get('_node_path');
    $service_name = $record->get('_service_name');

    $actions = $authorizer->getAccessibleActions($path, $service_name);

    return $record->set('actions', $actions);
  }

  function setAuthorizer(&$authorizer)
  {
    $this->authorizer =& $authorizer;
  }

  function & getAuthorizer()
  {
    if (is_object($this->authorizer))
      return $this->authorizer;

    $toolkit =& Limb :: toolkit('SimpleACL');
    $this->authorizer =& $toolkit->getAuthorizer();

    return $this->authorizer;
  }
}

?>
