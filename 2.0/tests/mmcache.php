<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/

if (function_exists("mmcache")) {
  mmcache();
} else {
  echo "<html><head><title>Turck MMCache</title></head><body><h1 align=\"center\">Turck MMCache is not installed</h1></body></html>";
}
?>