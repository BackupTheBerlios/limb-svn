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
require_once(dirname(__FILE__) . '/StatsReportDatasource.class.php');
require_once(dirname(__FILE__) . '/../reports/StatsIpsReport.class.php');

class StatsIpsListDatasource extends StatsReportDatasource
{
  function _initStatsReport()
  {
    $this->_stats_report = new StatsIpsReport();
  }

  function _processResultArray($arr)
  {
    $total = $this->_stats_report->fetchTotalHits();

    $result = array();
    foreach($arr as $index => $data)
    {
      $data['percentage'] = round($data['hits'] / $total * 100, 2);
      $result[$index] = $data;
    }

    return $result;
  }

  function _configureFilters()
  {
    $toolkit =& Limb :: toolkit();

    $this->_setPeriodFilter($toolkit->getRequest(), $toolkit->getLocale());
  }

  function _setPeriodFilter(&$request, &$locale)
  {
    $start_date = new Date();
    $start_date->setHour(0);
    $start_date->setMinute(0);
    $start_date->setSecond(0);

    if ($stats_start_date = $request->get('stats_start_date'))
      $start_date->setByLocaleString($locale, $stats_start_date, $locale->getShortDateTimeFormat());

    $finish_date = new Date();

    if ($stats_finish_date = $request->get('stats_finish_date'))
      $finish_date->setByLocaleString($locale, $stats_finish_date, $locale->getShortDateTimeFormat());

    $finish_date->setHour(23);
    $finish_date->setMinute(59);
    $finish_date->setSecond(59);

    $this->_stats_report->setPeriodFilter($start_date, $finish_date);
  }
}
?>