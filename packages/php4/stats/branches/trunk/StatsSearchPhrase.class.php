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
require_once(LIMB_DIR . '/class/lib/http/Uri.class.php');

class StatsSearchPhrase
{
  var $db = null;
  var $url = null;

  var $engine_rules = array();

  function StatsSearchPhrase()
  {
    $toolkit =& Limb :: toolkit();
    $this->db =& $toolkit->getDB();

    $this->url = new Uri();
  }

  function & instance()
  {
    if (!isset($GLOBALS['StatsSearchPhraseGlobalInstance']) || !is_a($GLOBALS['StatsSearchPhraseGlobalInstance'], 'StatsSearchPhrase'))
      $GLOBALS['StatsSearchPhraseGlobalInstance'] =& new StatsSearchPhrase();

    return $GLOBALS['StatsSearchPhraseGlobalInstance'];
  }

  function registerSearchEngineRule($engine_rule)
  {
    $this->engine_rules[] = $engine_rule;
  }

  function register($date)
  {
    if(!$rule =& $this->getMatchingSearchEngineRule())
      return false;

    $this->db->sqlInsert('sys_stat_search_phrase',
      array(
        'id' => null,
        'engine' => $rule->getEngineName(),
        'time' => $date->getStamp(),
        'phrase' => stripslashes(strip_tags($rule->getMatchingPhrase())),
      )
    );

    return true;
  }

  function getMatchingSearchEngineRule()
  {
    $uri = urldecode($this->_getHttpReferer());

    foreach(array_keys($this->engine_rules) as $id)
    {
      if($this->engine_rules[$id]->match($uri))
        return $this->engine_rules[$id];
    }
    return null;
  }

  function _getHttpReferer()
  {
    return isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
  }
}

?>