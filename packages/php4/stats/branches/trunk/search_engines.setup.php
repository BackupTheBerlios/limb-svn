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
require_once(dirname(__FILE__) . '/search_engine_rules/SearchEngineGoogleRule.class.php');
require_once(dirname(__FILE__) . '/search_engine_rules/SearchEngineYandexRule.class.php');
require_once(dirname(__FILE__) . '/search_engine_rules/SearchEngineMailruRule.class.php');
require_once(dirname(__FILE__) . '/search_engine_rules/SearchEngineRamblerRule.class.php');
require_once(dirname(__FILE__) . '/search_engine_rules/SearchEngineAportRule.class.php');

require_once(dirname(__FILE__) . '/StatsSearchPhrase.class.php');

$instance = StatsSearchPhrase :: instance();

$instance->registerSearchEngineRule( new SearchEngineGoogleRule());
$instance->registerSearchEngineRule( new SearchEngineYandexRule());
$instance->registerSearchEngineRule( new SearchEngineRamblerRule());
$instance->registerSearchEngineRule( new SearchEngineMailruRule());
$instance->registerSearchEngineRule( new SearchEngineAportRule());

?>