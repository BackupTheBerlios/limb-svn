<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: StatsHitsHostsByDaysReport.class.php 1032 2005-01-18 15:43:46Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/dao/SQLBasedDAO.class.php');

class StatsCountersReportDAO extends SQLBasedDAO
{
  function & _initSQL()
  {
    include_once(LIMB_DIR . '/core/db/ComplexSelectSQL.class.php');

    $toolkit =& Limb :: toolkit();
    $db_table = $toolkit->createDBTable('StatsDayCounters');
    $sql = new ComplexSelectSQL('SELECT %fields% FROM ' . $db_table->getTableName() . ' as master ' .
                                '%tables% %left_join% %where% %order% %group%');

    return $sql;
  }

  function & fetch()
  {
    include_once(LIMB_STATS_DIR . '/dao/criteria/StatsPeriodCriteria.class.php');
    $this->addCriteria(new StatsPeriodCriteria());

    include_once(LIMB_STATS_DIR . '/dao/StatsCountersReportRecordSet.class.php');
    return new StatsCountersReportRecordSet(parent :: fetch());
  }
}

?>
