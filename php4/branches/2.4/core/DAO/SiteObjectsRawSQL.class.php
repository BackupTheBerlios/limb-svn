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
require_once(LIMB_DIR . '/core/db/ComplexSelectSQL.class.php');

class SiteObjectsRawSQL extends ComplexSelectSQL
{
  function _defineRawSelectTemplate()
  {
    return
    "SELECT
    sso.modified_date as modified_date,
    sso.created_date as created_date,
    sso.creator_id as creator_id,
    sso.locale_id as locale_id
    %fields%,
    sso.title as title,
    sso.id as site_object_id,
    ssot.identifier as identifier,
    ssot.id as node_id,
    ssot.parent_id as parent_node_id,
    ssot.level as level,
    ssot.priority as priority,
    ssot.children as children,
    sys_class.id as class_id,
    sys_class.name as class_name,
    sys_behaviour.id as behaviour_id,
    sys_behaviour.name as behaviour,
    sys_behaviour.icon as icon,
    sys_behaviour.sort_order as sort_order,
    sys_behaviour.can_be_parent as can_be_parent
    FROM
    sys_site_object as sso,
    sys_class,
    sys_behaviour,
    sys_site_object_tree as ssot
    %tables%
    WHERE sys_class.id = sso.class_id
    AND sys_behaviour.id = sso.behaviour_id
    AND ssot.id = sso.node_id
    %where% %group% %order%";
  }

  function SiteObjectsRawSQL()
  {
    parent :: ComplexSelectSQL($this->_defineRawSelectTemplate());
  }
}

?>
