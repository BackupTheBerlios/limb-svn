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
require_once(LIMB_DIR . '/core/util/ini_support.inc.php');

if(!$HTMLSax_dir = getIniOption('external.ini', 'library_path', 'XML_HTMLSAX'))
  $HTMLSax_dir = '../external/pear/XML/';

define('XML_HTMLSAX3', $HTMLSax_dir);

require_once(XML_HTMLSAX3 . '/HTMLSax3.php');

?>