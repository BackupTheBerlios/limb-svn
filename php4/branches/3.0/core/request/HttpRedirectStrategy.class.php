<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: http_redirect_strategy.class.php 939 2004-12-04 14:30:44Z pachanga $
*
***********************************************************************************/

class HttpRedirectStrategy
{
  function redirect(&$http_response, $path)
  {
    $http_response->header("Location: {$path}");
  }
}

?>
