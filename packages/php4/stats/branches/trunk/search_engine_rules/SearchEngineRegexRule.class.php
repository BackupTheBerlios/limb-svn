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

class SearchEngineRegexRule //implements SearchEngineRule
{
  var $engine_name = '';
  var $regex = '';
  var $matches = array();
  var $uri = '';

  var $match_phrase_index;

  function SearchEngineRegexRule($engine_name, $regex, $match_phrase_index)
  {
    $this->engine_name = $engine_name;
    $this->regex = $regex;
    $this->match_phrase_index = $match_phrase_index;
  }

  function match($uri)
  {
    $this->uri = $uri;
    return preg_match($this->regex, $this->uri, $this->matches);
  }

  function getMatchingPhrase()
  {
    return $this->matches[$this->match_phrase_index];
  }

  function getEngineName()
  {
    return $this->engine_name;
  }
}

?>