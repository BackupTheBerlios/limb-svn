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
require_once(LIMB_DIR . '/class/lib/db/DbFactory.class.php');

function getCounterRecord()
{
  $db = DbFactory :: instance();
  $db->sqlSelect('sys_stat_counter', '*');
  return $db->fetchRow();
}

?>