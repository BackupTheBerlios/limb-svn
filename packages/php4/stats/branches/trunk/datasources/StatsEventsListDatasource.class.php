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
require_once(dirname(__FILE__) . '/../reports/StatsEventReport.class.php');

class StatsEventsListDatasource extends StatsReportDatasource
{
  protected $response_map = array(
        Request :: STATUS_SUCCESS => 'STATUS_SUCCESS',
        Request :: STATUS_FORM_DISPLAYED => 'STATUS_FORM_DISPLAYED',
        Request :: STATUS_FORM_SUBMITTED => 'STATUS_FORM_SUBMITTED',
        Request :: STATUS_FAILURE => 'STATUS_FAILURE',
        Request :: STATUS_FORM_NOT_VALID => 'STATUS_FORM_NOT_VALID'
      );

  protected function _initStatsReport()
  {
    $this->_stats_report = new StatsEventReport();
  }

  protected function _processResultArray($arr)
  {
    $result = array();
    foreach($arr as $index => $data)
    {
      $data[$this->response_map[$data['status']]] = 1;
      $result[$index] = $data;
    }

    return $result;
  }

  protected function _configureFilters()
  {
    $request = Limb :: toolkit()->getRequest();

    $this->_setIpFilter($request);

    $this->_setLoginFilter($request);

    $this->_setActionFilter($request);

    $this->_setPeriodFilter($request);

    $this->_setUriFilter($request);

    $this->_setStatusFilter($request);
  }

  protected function _setLoginFilter($request)
  {
    if ($stats_user_login = $request->get('stats_user_login'))
      $this->_stats_report->setLoginFilter($stats_user_login);
  }

  protected function _setActionFilter($request)
  {
    if ($stats_action_name = $request->get('stats_action_name'))
      $this->_stats_report->setActionFilter($stats_action_name);
  }

  protected function _setIpFilter($request)
  {
    if ($stats_ip = $request->get('stats_ip'))
      $this->_stats_report->setIpFilter($stats_ip);
  }

  protected function _setStatusFilter($request)
  {
    if (($stats_status = $request->get('stats_status')) ||  (!is_array($stats_status)))
      return ;

    $status_mask = 0;
    $response_keys = array_keys($this->response_map);
    foreach($stats_status as $index => $on)
      if (isset($response_keys[$index]))
        $status_mask = $status_mask | $response_keys[$index];

    if ($status_mask)
      $this->_stats_report->setStatusFilter($status_mask);
  }

  protected function _setUriFilter($request)
  {
    if ($stats_uri = $request->get('stats_uri'))
      $this->_stats_report->setUriFilter($stats_uri);
  }

  protected function _setPeriodFilter($request)
  {
    $locale = Limb :: toolkit()->getLocale();
    $start_date = new Date();
    $start_date->setHour(0);
    $start_date->setMinute(0);
    $start_date->setSecond(0);

    if ($stats_start_date = $request->get('stats_start_date'))
      $start_date->setByLocaleString($locale, $stats_start_date, $locale->getShortDateTimeFormat());

    if ($stats_start_hour = $request->get('stats_start_hour'))
      $start_date->setHour($stats_start_hour);

    if ($stats_start_minute = $request->get('stats_start_minute'))
      $start_date->setMinute($stats_start_minute);

    $finish_date = new Date();
    if ($stats_finish_date = $request->get('stats_finish_date'))
      $finish_date->setByLocaleString($locale, $stats_finish_date, $locale->getShortDateTimeFormat());

    $finish_date->setHour(23);
    $finish_date->setMinute(59);
    $finish_date->setSecond(59);

    if ($stats_finish_hour = $request->get('stats_finish_hour'))
      $finish_date->setHour($stats_finish_hour);

    if ($stats_finish_minute = $request->get('stats_finish_minute'))
      $finish_date->setMinute($stats_finish_minute);

    $this->_stats_report->setPeriodFilter($start_date, $finish_date);
  }
}
?>
