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
require_once(LIMB_DIR . '/core/Limb.class.php');

function getCounterRecord()
{
  $toolkit =& Limb :: toolkit();
  $db =& new SimpleDb($toolkit->getDbConnection());
  $db->select('sys_stat_counter', '*');
  return $db->getRow();
}

?>