<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/

function file_get_contents($filename)
{
  $fd = fopen("$filename", "rb");
  $content = fread($fd, filesize($filename));
  fclose($fd);
  return $content;
}

function html_entity_decode($str, $style=NULL)
{
  return strtr($str, array_flip(get_html_translation_table(HTML_ENTITIES)));
}

?>