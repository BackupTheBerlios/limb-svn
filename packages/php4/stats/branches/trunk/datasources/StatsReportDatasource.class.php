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

class StatsReportDatasource //implements Datasource
{
  var $_stats_report;

  function _initStatsReport(){die('abstract function!')}

  function StatsReportDatasource()
  {
    $this->_initStatsReport();
  }

  function getDataset(&$counter, $params=array())
  {
    $this->_configureFilters();

    $counter = $this->_stats_report->fetchCount($params);
    $raw_data = $this->_stats_report->fetch($params);

    $result = $this->_processResultArray($raw_data);

    return new ArrayDataset($result);
  }

  function _configureFilters(){die('abstract function!')}

  function _processResultArray($arr){die('abstract function!')}
}
?>