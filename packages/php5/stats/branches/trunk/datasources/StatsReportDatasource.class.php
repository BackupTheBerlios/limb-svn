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
require_once(LIMB_DIR . '/class/datasources/Datasource.interface.php');

abstract class StatsReportDatasource implements Datasource
{
  protected $_stats_report;

  abstract protected function _initStatsReport();

  public function __construct()
  {
    $this->_initStatsReport();
  }

  public function getDataset(&$counter, $params=array())
  {
    $this->_configureFilters();

    $counter = $this->_stats_report->fetchCount($params);
    $raw_data = $this->_stats_report->fetch($params);

    $result = $this->_processResultArray($raw_data);

    return new ArrayDataset($result);
  }

  abstract protected function _configureFilters();

  abstract protected function _processResultArray($arr);
}
?>