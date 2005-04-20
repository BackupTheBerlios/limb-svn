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
require_once(LIMB_DIR . '/core/model/stats/stats_referers_report.class.php');
require_once(LIMB_DIR . '/core/lib/date/date.class.php');

class stats_referers_list_datasource extends datasource
{
  var $request;
  var $stats_referers_report = null;

  function stats_referers_list_datasource()
  {
    $this->stats_report =& $this->_create_referers_report();
    parent :: datasource();
  }

  function set_request(&$request)
  {
    $this->request =& $request;
  }

  function & _get_request()
  {
    if(is_object($this->request))
      return $this->request;

    //ugly!?
    return request :: instance();
  }

  function _create_referers_report()
  {
    return new stats_referers_report();
  }

  function & get_dataset(&$counter, $params=array())
  {
    $this->_configure_filters();

    $limit = isset($params['limit']) ? $params['limit'] : 0;
    $offset = isset($params['offset']) ? $params['offset'] : 0;

    $counter = $this->_do_fetch_count();
    $arr = $this->_process_result_array($this->_do_fetch($limit, $offset));

    return new array_dataset($arr);
  }

  function _do_fetch($limit, $offset)
  {
    return $this->stats_report->fetch($limit, $offset);
  }

  function _do_fetch_count()
  {
    return $this->stats_report->fetch_count();
  }

  function _process_result_array($arr)
  {
    if(!sizeof($arr))
      return $arr;

    $total = $this->stats_report->fetch_total_hits();

    $result = array();
    foreach($arr as $index => $data)
    {
      $data['percentage'] = round($data['hits'] / $total * 100, 2);

      $result[$index] = $data;
    }
    return $result;
  }

  function _configure_filters()
  {
    $this->_set_period_filter();
  }

  function _set_period_filter()
  {
    $request =& $this->_get_request();

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