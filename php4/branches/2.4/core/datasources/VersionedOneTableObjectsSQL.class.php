<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: OneTableObjectsSQL.class.php 1068 2005-01-28 14:01:40Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/db/ComplexSelectSQLDecorator.class.php');

class VersionedOneTableObjectsSQL extends ComplexSelectSQLDecorator
{
  function VersionedOneTableObjectsSQL(&$sql)
  {
    parent :: ComplexSelectSQLDecorator($sql);

    $this->addCondition('sso.current_version=tn.version');
  }
}

?>
