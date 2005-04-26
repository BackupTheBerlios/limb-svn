<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: InterceptingFilter.interface.php 981 2004-12-21 15:51:00Z pachanga $
*
***********************************************************************************/

class ResponseProcessingFilter//implements InterceptingFilter
{
  function run(&$filter_chain, &$request, &$response, &$context)
  {
    ob_start();

    $filter_chain->next();

    if($response->getContentType() == 'text/html' &&
       $response->getStatus() == 200)//only 200?
    {
      if (Debug :: isConsoleEnabled())
        $response->append(Debug :: parseHtmlConsole());

      $response->append(MessageBox :: parse());//It definitely should be somewhere else!
    }

    $response->commit();

    ob_end_flush();
  }
}

?>