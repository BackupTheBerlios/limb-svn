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
require_once(LIMB_DIR . '/core/date/Date.class.php');

class StatsPeriodCriteria
{
  function process(&$sql)
  {
    $toolkit =& Limb :: toolkit();

    $request =& $toolkit->getRequest();

    $start_date = new Date();
    $start_date->setHour(0);
    $start_date->setMinute(0);
    $start_date->setSecond(0);

    if ($stats_start_date = $request->get('start_date'))
      $start_date->setByString($stats_start_date);

    $finish_date = new Date();
    if ($stats_finish_date = $request->get('finish_date'))
      $finish_date->setByString($stats_finish_date);

    $finish_date->setHour(23);
    $finish_date->setMinute(59);
    $finish_date->setSecond(59);

    $start_stamp = $start_date->getStamp();
    $finish_stamp = $finish_date->getStamp();

    $sql->addCondition("master.time BETWEEN {$start_stamp} AND {$finish_stamp}");
  }
}

?>
