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

class StatsKeywordsReportDAO extends SQLBasedDAO
{
  function & _initSQL()
  {
    include_once(LIMB_DIR . '/core/db/ComplexSelectSQL.class.php');

    $toolkit =& Limb :: toolkit();
    $sql = new ComplexSelectSQL('SELECT master.phrase, master.engine, COUNT(phrase) as hits %fields% FROM ' .
                                 'stats_search_phrase as master ' .
                                '%tables% %left_join% %where% ' .
                                'GROUP BY phrase %group%' .
                                'ORDER BY hits DESC %order%');

    return $sql;
  }

  function & fetch()
  {
    include_once(dirname(__FILE__) . '/criteria/StatsPeriodCriteria.class.php');
    $this->addCriteria(new StatsPeriodCriteria());

    include_once(dirname(__FILE__) . '/StatsSearchReportsPercentageRecordSet.class.php');
    return new StatsSearchReportsPercentageRecordSet(parent :: fetch());
  }
}
?>