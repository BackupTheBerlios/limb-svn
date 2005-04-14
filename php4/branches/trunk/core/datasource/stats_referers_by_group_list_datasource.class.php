<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: stats_referers_list_datasource.class.php 916 2004-11-23 09:14:28Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/datasource/stats_referers_list_datasource.class.php');

class stats_referers_by_group_list_datasource extends stats_referers_list_datasource
{
  function _get_group()
  {
    $request = request :: instance();
    return $request->get_attribute('group');
  }

  function _do_fetch($limit, $offset)
  {
    if($group = $this->_get_group())
      return $this->stats_report->fetch_by_group($group, $limit, $offset);
    else
      return array();
  }

  function _do_fetch_count()
  {
    if($group = $this->_get_group())
      return $this->stats_report->fetch_count_by_group($group);
    else
      return array();
  }
}
?>