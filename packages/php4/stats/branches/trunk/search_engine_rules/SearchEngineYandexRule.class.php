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
require_once(dirname(__FILE__) . '/SearchEngineRegexRule.class.php');

class SearchEngineYandexRule extends SearchEngineRegexRule
{
  function SearchEngineYandexRule()
  {
    parent :: SearchEngineRegexRule('yandex', '/^.*yand.*text=([^&]*).*$/', 1);
  }

  function getMatchingPhrase()
  {
    $phrase = parent :: getMatchingPhrase();

    if(strpos($this->uri, 'yandpage') !== false)
      $phrase = convert_cyr_string(urldecode($phrase), 'k', 'w');

    return $phrase;
  }

}

?>