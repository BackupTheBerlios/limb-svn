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
  public function storageOpen();
  public function storageClose();
  public function storageRead($session_id);
  public function storageWrite($session_id, $value);
  public function storageDestroy($session_id);
  public function storageDestroyUser($user_id);
  public function storageGc($max_life_time);
}
?>