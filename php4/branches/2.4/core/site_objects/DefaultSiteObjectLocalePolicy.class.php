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

class DefaultSiteObjectLocalePolicy
{
  function assign(&$site_object)
  {
    if(!$site_object->getLocaleId())
      $site_object->setLocaleId($this->getParentLocaleId($site_object->getParentNodeId()));
  }

  function getParentLocaleId($parent_node_id)
  {
    $sql = "SELECT sso.locale_id as locale_id
            FROM sys_site_object as sso, sys_site_object_tree as ssot
            WHERE ssot.id = :parent_node_id:
            AND sso.id = ssot.object_id";

    $toolkit =& Limb :: toolkit();
    $conn =& $toolkit->getDbConnection();
    $stmt =& $conn->newStatement($sql);
    $stmt->setInteger('parent_node_id', $parent_node_id);

    $record = $stmt->getOneRecord();

    if($record)
      return $record->get('locale_id');
    else
      return $toolkit->constant('DEFAULT_CONTENT_LOCALE_ID');
  }
}
?>
