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
require_once(LIMB_DIR . '/core/http/Uri.class.php');
require_once(LIMB_DIR . '/core/date/Date.class.php');

class StatsSearchPhraseRegister
{
  var $db_table = null;
  var $url = null;

  var $engine_rules = array();

  function StatsSearchPhraseRegister()
  {
    $toolkit =& Limb :: toolkit();
    $this->db_table =& $toolkit->createDBTable('StatsSearchPhrase');

    $this->url = new Uri();
  }

  function registerSearchEngineRule(&$engine_rule)
  {
    $this->engine_rules[] =& $engine_rule;
  }

  function register($stats_request)
  {
    if(!$rule =& $this->getMatchingSearchEngineRule($stats_request->getRefererUri()))
      return;

    $this->db_table->insert(array(
                              'id' => null,
                              'engine' => $rule->getEngineName(),
                              'time' => $stats_request->getTime(),
                              'phrase' => stripslashes(strip_tags($rule->getMatchingPhrase()))));

    return true;
  }

  function & getMatchingSearchEngineRule(&$uri)
  {
    $url = urldecode($uri->toString());

    foreach(array_keys($this->engine_rules) as $id)
    {
      if($this->engine_rules[$id]->match($url))
        return $this->engine_rules[$id];
    }
    return null;
  }
}

?>