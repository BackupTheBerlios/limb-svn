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

interface session_driver
{
  public function storage_open();
  public function storage_close();
  public function storage_read($session_id);
  public function storage_write($session_id, $value);
  public function storage_destroy($session_id);
  public function storage_destroy_user($user_id);
  public function storage_gc($max_life_time);
}
?>