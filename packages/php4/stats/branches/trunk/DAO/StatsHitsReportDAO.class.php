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
require_once(LIMB_DIR . '/core/DAO/SQLBasedDAO.class.php');

class StatsHitsReportDAO extends SQLBasedDAO
{
  function & _initSQL()
  {
    include_once(LIMB_DIR . '/core/db/ComplexSelectSQL.class.php');

    $toolkit =& Limb :: toolkit();
    $db_table = $toolkit->createDBTable('StatsHit');
    $sql = new ComplexSelectSQL('SELECT %fields% FROM ' . $db_table->getTableName() . ' as master ' .
                                '%tables% %left_join% %where% %order% %group%');

    return $sql;
  }

  function & fetch()
  {
    include_once(LIMB_STATS_DIR . '/DAO/criteria/StatsPeriodCriteria.class.php');
    $this->addCriteria(new StatsPeriodCriteria());

    return parent :: fetch();
  }
}

?>
