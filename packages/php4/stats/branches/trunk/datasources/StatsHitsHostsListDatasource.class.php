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
require_once(dirname(__FILE__) . '/../reports/StatsHitsHostsByDaysReport.class.php');

class StatsHitsHostsListDatasource
{
  function _processResultArray($arr)
  {
    if(ComplexArray :: getMaxColumnValue('hosts', $arr, $index) !== false)
      $arr[$index]['max_hosts'] = 1;

    if(ComplexArray :: getMaxColumnValue('hits', $arr, $index) !== false)
      $arr[$index]['max_hits'] = 1;

    if(ComplexArray :: getMaxColumnValue('home_hits', $arr, $index) !== false)
      $arr[$index]['max_home_hits'] = 1;

    if(ComplexArray :: getMaxColumnValue('audience', $arr, $index) !== false)
      $arr[$index]['max_audience'] = 1;

    $result = array();
    foreach($arr as $index => $data)
    {
      if(date('w', $data['time'] + 60*60*24) == 1)
        $data['new_week'] = 1;

      $result[$index] = $data;
    }

    return $result;
  }
}
?>