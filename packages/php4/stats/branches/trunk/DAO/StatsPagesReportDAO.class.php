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
require_once(LIMB_DIR . '/core/dao/SQLBasedDAO.class.php');

class StatsPagesReportDAO extends SQLBasedDAO
{
  function & _initSQL()
  {
    include_once(LIMB_DIR . '/core/db/ComplexSelectSQL.class.php');

    $toolkit =& Limb :: toolkit();
    $sql = new ComplexSelectSQL('SELECT stats_uri.uri, stats_uri.id as stats_uri_id, COUNT(stats_uri_id) as hits'.
                                ' %fields% FROM ' .
                                ' stats_uri, stats_hit as master' .
                                '%tables% %left_join% '.
                                'WHERE master.stats_uri_id = stats_uri.id %where% ' .
                                'GROUP BY uri %group%' .
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