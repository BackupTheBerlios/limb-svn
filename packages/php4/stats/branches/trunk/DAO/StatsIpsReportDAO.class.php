<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: StatsIpsListDatasource.class.php 972 2004-12-20 15:58:13Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/DAO/SQLBasedDAO.class.php');

class StatsIpsReportDAO extends SQLBasedDAO
{
  function & _initSQL()
  {
    include_once(LIMB_DIR . '/core/db/ComplexSelectSQL.class.php');

    $toolkit =& Limb :: toolkit();
    $db_table = $toolkit->createDBTable('StatsHit');
    $sql = new ComplexSelectSQL('SELECT ip, COUNT(ip) as hits %fields% FROM ' .
                                 $db_table->getTableName() . ' as master ' .
                                '%tables% %left_join% %where% ' .
                                'GROUP BY ip %group%' .
                                'ORDER BY hits DESC %order%');

    return $sql;
  }

  function & fetch()
  {
    include_once(dirname(__FILE__) . '/criteria/StatsPeriodCriteria.class.php');
    $this->addCriteria(new StatsPeriodCriteria());

    include_once(dirname(__FILE__) . '/StatsPercentageRecordSet.class.php');
    return new StatsPercentageRecordSet(parent :: fetch());
  }
}
?>