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
require_once(LIMB_DIR . '/core/lib/util/ini.class.php');

if(!$HTMLSax_dir = get_ini_option('external.ini', 'library_path', 'XML_HTMLSAX'))
  $HTMLSax_dir = '../external/pear/XML/';
  
define('XML_HTMLSAX3', $HTMLSax_dir);

?>