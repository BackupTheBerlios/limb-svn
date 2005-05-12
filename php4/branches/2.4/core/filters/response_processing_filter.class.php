<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: output_buffering_filter.class.php 916 2004-11-23 09:14:28Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/filters/intercepting_filter.class.php');

class response_processing_filter extends intercepting_filter
{
  function run(&$filter_chain, &$request, &$response)
  {
    $filter_chain->next();

    if( $response->get_content_type() == 'text/html' &&
        $response->get_status() == 200)//only 200?
    {
      if (debug :: is_console_enabled())
        $response->write(debug :: parse_html_console());

      $response->write(message_box :: parse());//It definetly should be somewhere else!
    }
  }
}
?>