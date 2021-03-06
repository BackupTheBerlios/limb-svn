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
require_once(LIMB_DIR . '/core/lib/system/objects_support.inc.php');

class stats_search_phrase
{
  var $db = null;
  var $url = null;

  var $engine_rules = array();

  function stats_search_phrase()
  {
    $this->db =& db_factory :: instance();
    $this->url = new uri();
  }

  function & instance()
  {
    return instantiate_object('stats_search_phrase');
  }

  function register_search_engine_rule(&$engine_rule)
  {
    $this->engine_rules[] =& $engine_rule;
  }

  function register($date)
  {
    if(!$rule = $this->_get_matching_search_engine_rule())
      return false;

    $this->db->sql_insert('sys_stat_search_phrase',
      array(
        'engine' => $rule->get_engine_name(),
        'time' => $date->get_stamp(),
        'phrase' => stripslashes(strip_tags($rule->get_matching_phrase())),
      )
    );

    return true;
  }

  function _get_matching_search_engine_rule()
  {
    $uri = urldecode($this->_get_http_referer());

    foreach(array_keys($this->engine_rules) as $id)
    {
      if($this->engine_rules[$id]->match($uri))
        return $this->engine_rules[$id];
    }
    return null;
  }

  function _get_http_referer()
  {
    return isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
  }
}

?>