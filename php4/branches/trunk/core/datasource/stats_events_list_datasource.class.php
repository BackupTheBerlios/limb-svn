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
require_once(LIMB_DIR . '/core/model/stats/stats_event_report.class.php');

class stats_events_list_datasource extends datasource
{
  var $status_map = array(
        REQUEST_STATUS_SUCCESS => 'SUCCESS',
        REQUEST_STATUS_FORM_DISPLAYED => 'FORM_DISPLAYED',
        REQUEST_STATUS_FORM_SUBMITTED => 'FORM_SUBMITTED',
        REQUEST_STATUS_FAILURE => 'FAILURE',
        REQUEST_STATUS_FORM_NOT_VALID => 'FORM_NOT_VALID'
      );

  var $stats_event_report = null;

  function stats_events_list_datasource()
  {
    $this->stats_event_report =& new stats_event_report();

    parent :: datasource();
  }

  function & get_dataset(&$counter, $params=array())
  {
    $this->_configure_stats_event_report_filter();

    $counter = $this->stats_event_report->fetch_count($params);
    $arr = $this->stats_event_report->fetch($params);

    $this->_assign_status_hints_to_result_array($arr);

    return new array_dataset($arr);
  }

  function _assign_status_hints_to_result_array(&$arr)
  {
    foreach($arr as $index => $data)
    {
      if(isset($this->status_map[$data['status']]))
        $arr[$index][$this->status_map[$data['status']]] = 1;
      else
        $arr[$index]['DEFAULT'] = 1;
    }
  }

  function _configure_stats_event_report_filter()
  {
    $request = request :: instance();

    $this->_set_ip_filter($request);

    $this->_set_user_filter($request);

    $this->_set_action_filter($request);

    $this->_set_period_filter($request);

    $this->_set_uri_filter($request);

    $this->_set_status_filter($request);
  }

  function _set_user_filter(&$request)
  {
    if ($stats_user_login = $request->get_attribute('stats_user_login'))
      $this->stats_event_report->set_user_filter($stats_user_login);
  }

  function _set_action_filter(&$request)
  {
    if ($stats_action_name = $request->get_attribute('stats_action_name'))
      $this->stats_event_report->set_action_filter($stats_action_name);
  }

  function _set_ip_filter(&$request)
  {
    if ($stats_ip = $request->get_attribute('stats_ip'))
      $this->stats_event_report->set_ip_filter($stats_ip);
  }

  function _set_status_filter(&$request)
  {
    $stats_status = $request->get_attribute('stats_status');

    if (!$stats_status || !is_array($stats_status))
      return ;

    $status_mask = 0;
    $response_keys = array_keys($this->status_map);
    foreach($stats_status as $index => $on)
      if (isset($response_keys[$index]))
        $status_mask = $status_mask | $response_keys[$index];

    if ($status_mask)
      $this->stats_event_report->set_status_filter($status_mask);
  }

  function _set_uri_filter(&$request)
  {
    if ($stats_uri = $request->get_attribute('stats_uri'))
      $this->stats_event_report->set_uri_filter($stats_uri);
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

    if ($stats_start_hour = $request->get_attribute('stats_start_hour'))
      $start_date->set_hour($stats_start_hour);

    if ($stats_start_minute = $request->get_attribute('stats_start_minute'))
      $start_date->set_minute($stats_start_minute);

    $finish_date = new date();
    if ($stats_finish_date = $request->get_attribute('stats_finish_date'))
      $finish_date->set_by_string($stats_finish_date, $locale->get_short_date_time_format());

    $finish_date->set_hour(23);
    $finish_date->set_minute(59);
    $finish_date->set_second(59);

    if ($stats_finish_hour = $request->get_attribute('stats_finish_hour'))
      $finish_date->set_hour($stats_finish_hour);

    if ($stats_finish_minute = $request->get_attribute('stats_finish_minute'))
      $finish_date->set_minute($stats_finish_minute);

    $this->stats_event_report->set_period_filter($start_date, $finish_date);
  }
}
?>
