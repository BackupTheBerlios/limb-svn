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

class stats_referers_except_groups_list_datasource extends stats_referers_list_datasource
{
  var $groups = array();

  function _get_groups()
  {
    if($this->groups)
      return $this->groups;

    if(ini_exists('referers_groups.ini'))
      $this->groups = get_ini_option('referers_groups.ini', 'groups');

    return $this->groups;
  }

  function _do_fetch($limit, $offset)
  {
    return $this->stats_report->fetch_except_groups($this->_get_groups(), $limit, $offset);
  }

  function _do_fetch_count()
  {
    return $this->stats_report->fetch_count_except_groups($this->_get_groups());
  }
}
?>