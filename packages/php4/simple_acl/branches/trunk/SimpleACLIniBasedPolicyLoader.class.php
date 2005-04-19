<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: SimpleAuthorizer.class.php 1032 2005-01-18 15:43:46Z pachanga $
*
***********************************************************************************/
require_once(WACT_ROOT . '/datasource/dataspace.inc.php');

class SimpleACLIniBasedPolicyLoader
{
  function load(&$authorizor)
  {
    $toolkit =& Limb :: toolkit();
    $ini =& $toolkit->getIni('acl.ini');

    if(catch_error('LimbException', $e))
      return;

    if(!$ini->hasOption('policy'))
      return;

    $policy_lines = $ini->getOption('policy');

    foreach($policy_lines as $line)
    {
      $policy = preg_split("/[\s,]+/", $line);;
      $authorizor->attachPolicy($policy[0], $policy[1], (int)$policy[2]);
    }
  }
}
?>