<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: SiteObjectsRawSQL.class.php 1085 2005-02-02 16:04:20Z pachanga $
*
***********************************************************************************/
class ServicesCriteria
{
  function process(&$sql)
  {
    $sql->addField('sys_service.*');
    $sql->addTable('sys_service');
    $sql->addCondition('sys_object.oid=sys_service.oid');
  }
}

?>
