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

interface SessionDriver
{
  function storageOpen();
  function storageClose();
  function storageRead($session_id);
  function storageWrite($session_id, $value);
  function storageDestroy($session_id);
  function storageDestroyUser($user_id);
  function storageGc($max_life_time);
}
?>