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
require_once(dirname(__FILE__) . '/SearchEngineRule.interface.php');

class SearchEngineRegexRule implements SearchEngineRule
{
  protected $engine_name = '';
  protected $regex = '';
  protected $matches = array();
  protected $uri = '';

  protected $match_phrase_index;

  function __construct($engine_name, $regex, $match_phrase_index)
  {
    $this->engine_name = $engine_name;
    $this->regex = $regex;
    $this->match_phrase_index = $match_phrase_index;
  }

  public function match($uri)
  {
    $this->uri = $uri;
    return preg_match($this->regex, $this->uri, $this->matches);
  }

  public function getMatchingPhrase()
  {
    return $this->matches[$this->match_phrase_index];
  }

  public function getEngineName()
  {
    return $this->engine_name;
  }
}

?>