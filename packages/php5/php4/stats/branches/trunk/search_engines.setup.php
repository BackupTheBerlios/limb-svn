<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(dirname(__FILE__) . '/search_engine_rules/search_engine_google_rule.class.php');
require_once(dirname(__FILE__) . '/search_engine_rules/search_engine_yandex_rule.class.php');
require_once(dirname(__FILE__) . '/search_engine_rules/search_engine_mailru_rule.class.php');
require_once(dirname(__FILE__) . '/search_engine_rules/search_engine_rambler_rule.class.php');
require_once(dirname(__FILE__) . '/search_engine_rules/search_engine_aport_rule.class.php');

require_once(dirname(__FILE__) . '/stats_search_phrase.class.php');

$instance =& stats_search_phrase :: instance();

$instance->register_search_engine_rule( new search_engine_google_rule());
$instance->register_search_engine_rule( new search_engine_yandex_rule());
$instance->register_search_engine_rule( new search_engine_rambler_rule());
$instance->register_search_engine_rule( new search_engine_mailru_rule());
$instance->register_search_engine_rule( new search_engine_aport_rule());

?>