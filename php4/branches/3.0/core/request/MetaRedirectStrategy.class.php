<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: meta_redirect_strategy.class.php 939 2004-12-04 14:30:44Z pachanga $
*
***********************************************************************************/

class MetaRedirectStrategy
{
  var $template_path;

  function MetaRedirectStrategy($template_path = null)
  {
    $this->template_path = $template_path;
  }

  function redirect(&$http_response, $path)
  {
    $http_response->write($this->_prepareDefaultResponse('Redirecting...', $path));
  }

  function _prepareDefaultResponse($message, $path)
  {
    return "<html><head><meta http-equiv=refresh content='0;url={$path}'></head>
            <body bgcolor=white>{$message}</body></html>";
  }

}

?>
