<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: Service.class.php 1191 2005-03-25 14:04:13Z seregalimb $
*
***********************************************************************************/
class NotFoundRequestResolver //implements RequestResolver
{
  function & getRequestedService(&$request)
  {
    include_once(LIMB_DIR . '/core/services/Service.class.php');
    return new Service('404');
  }

  function getRequestedAction(&$request)
  {
    return 'display';
  }

  function getRequestedEntity(&$request)
  {
    return new Object();
  }
}

?>