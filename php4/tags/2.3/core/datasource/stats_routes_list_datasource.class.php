<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/

require_once(LIMB_DIR . '/core/datasource/datasource.class.php');
require_once(LIMB_DIR . '/core/model/stats/stats_routes_report.class.php');

class stats_routes_list_datasource extends datasource
{
  var $stats_routes_report = null;

  function stats_routes_list_datasource()
  {
    $this->stats_report =& new stats_routes_report();

    parent :: datasource();
  }

  function & get_dataset(&$counter, $params=array())
  {
    $this->_configure_filters();

    $counter = $this->stats_report->fetch_count($params);
    $arr = $this->stats_report->fetch($params);

    $arr = $this->_process_result_array($arr);

    return new array_dataset($arr);
  }

  function _process_result_array($arr)
  {
    return $arr;
  }

  function _configure_filters()
  {
    $request = request :: instance();

    $this->_set_period_filter($request);
  }

  function _set_period_filter(&$request)
  {
    $locale =& locale :: instance();
    $start_date = new date();
    $start_date->set_hour(0);
    $start_date->set_minute(0);
    $start_date->set_second(0);

    if ($stats_start_date = $request->get_attribute('stats_start_date'))
      $start_date->set_by_string($stats_start_date, $locale->get_short_date_time_format());

    $finish_date = new date();

    if ($stats_finish_date = $request->get_attribute('stats_finish_date'))
      $finish_date->set_by_string($stats_finish_date, $locale->get_short_date_time_format());

    $finish_date->set_hour(23);
    $finish_date->set_minute(59);
    $finish_date->set_second(59);

    $this->stats_report->set_period_filter($start_date, $finish_date);
  }
}
?>