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
require_once(dirname(__FILE__) . '/search_engine_regex_rule.class.php');

class search_engine_yandex_rule extends search_engine_regex_rule
{
  public function __construct()
  {
    parent :: __construct('yandex', '/^.*yand.*text=([^&]*).*$/', 1);
  }

  public function get_matching_phrase()
  {
    $phrase = parent :: get_matching_phrase();

    if(strpos($this->uri, 'yandpage') !== false)
      $phrase = convert_cyr_string(urldecode($phrase), 'k', 'w');

    return $phrase;
  }

}

?>