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

class http_redirect_strategy
{
  function redirect(&$http_response, $path)
  {
    $http_response->header("Location: {$path}");
  }
}

?>
