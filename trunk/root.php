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
setlocale(LC_ALL, 'ru');//temporary

require_once(LIMB_DIR . 'core/lib/debug/debug.class.php');

debug :: add_timing_point('start');

require_once(LIMB_DIR . 'core/filters/filter_chain.class.php');
require_once(LIMB_DIR . 'core/request/response.class.php');
require_once(LIMB_DIR . 'core/request/request.class.php');
require_once(LIMB_DIR . 'core/lib/http/control_flow.inc.php');

// filters include
require_once(LIMB_DIR . 'core/filters/logging_filter.class.php');
require_once(LIMB_DIR . 'core/filters/locale_definition_filter.class.php');
require_once(LIMB_DIR . 'core/filters/authentication_filter.class.php');
require_once(LIMB_DIR . 'core/filters/output_buffering_filter.class.php');
require_once(LIMB_DIR . 'core/filters/site_object_controller_filter.class.php');

$request =& request :: instance();
$response =& response :: instance();

$filter_chain =& new filter_chain($request, $response);

$filter_chain->register_filter(new locale_definition_filter());
$filter_chain->register_filter(new authentication_filter());
$filter_chain->register_filter(new logging_filter());
$filter_chain->register_filter(new output_buffering_filter());
$filter_chain->register_filter(new site_object_controller_filter());

$filter_chain->process();

if (debug :: is_console_enabled())
	echo debug :: parse_html_console();

$response->commit();

?>